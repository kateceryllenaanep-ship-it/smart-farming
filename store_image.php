<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    header('Content-Type: application/json');

    // ----------------------
    // DB CONNECTION
    // ----------------------
    $conn = new mysqli(
        "localhost",
        "root",
        "",
        "smartfarm_db"
    );

    if ($conn->connect_error) {
        die(json_encode([
            "status" => "error",
            "message" => "DB connection failed: " . $conn->connect_error
        ]));
    }

    // ----------------------
    // READ JSON INPUT
    // ----------------------
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        die(json_encode([
            "status" => "error",
            "message" => "Invalid JSON input"
        ]));
    }

    // ----------------------
    // EXTRACT DATA
    // ----------------------
    $imageBase64 = $data['image'] ?? null;
    $detections = $data['detections'] ?? [];

    if (!$imageBase64) {
        die(json_encode([
            "status" => "error",
            "message" => "No image received"
        ]));
    }

    // Strip data URI prefix if present
    $imageBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64);

    // ----------------------
    // DECODE IMAGE
    // ----------------------
    $imageData = base64_decode($imageBase64);

    if ($imageData === false) {
        die(json_encode([
            "status" => "error",
            "message" => "Failed to decode image"
        ]));
    }

    // ----------------------
    // SAVE IMAGE
    // ----------------------
    $timestamp = date("Y-m-d_H-i-s");
    $directory = __DIR__ . "/assets/images/snapshots/";
    $webDirectory = "assets/images/snapshots/";

    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    $filename = "capture_" . $timestamp . ".jpg";
    $filePath = $directory . $filename;
    $dbPath = $webDirectory . $filename;

    file_put_contents($filePath, $imageData);

    // ----------------------
    // INSERT SNAPSHOT
    // ----------------------
    $device_id = 1;
    $source = "detection";

    $stmt = $conn->prepare("
        INSERT INTO snapshots(device_id, image_path, source, created_at)
        VALUES (?, ?, ?, NOW())
    ");

    $stmt->bind_param("iss", $device_id, $dbPath, $source);
    $stmt->execute();

    $snapshot_id = $stmt->insert_id;
    $stmt->close();

    // ----------------------
    // PREPARE GROUPED DATA
    // ----------------------
    $labels = [];
    $coords_list = [];
    $raw_list = [];
    $max_confidence = 0;
    $detection_type = "Object";

    foreach ($detections as $det) {

        if (!isset($det['label']) || !isset($det['confidence'])) {
            continue;
        }

        $label = $det['label'];
        $confidence = floatval($det['confidence'] * 100);

        $labels[] = $label;

        if ($confidence > $max_confidence) {
            $max_confidence = $confidence;
            $detection_type = classifyCategory($label);
        }

        $coords_list[] = [
            "label" => $label,
            "x" => $det['x'],
            "y" => $det['y'],
            "width" => $det['width'],
            "height" => $det['height']
        ];

        $raw_list[] = $det;
    }

    // Remove duplicates (optional but recommended)
    $labels = array_values(array_unique($labels));

    // Convert to JSON
    $objects = json_encode($labels);
    $coords = json_encode($coords_list);
    $raw_json = json_encode($raw_list);

    // ----------------------
    // INSERT SINGLE DETECTION ROW
    // ----------------------
    $stmt = $conn->prepare("
        INSERT INTO detections(
            device_id,
            snapshot_id,
            detection_type,
            objects,
            confidence,
            triggered_at,
            raw_json,
            coords
        )
        VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)
    ");

    $stmt->bind_param(
        "iissdss",
        $device_id,
        $snapshot_id,
        $detection_type,
        $objects,
        $max_confidence,
        $raw_json,
        $coords
    );

    $stmt->execute();
    $detection_id = $stmt->insert_id;
    $stmt->close();

    // ----------------------
    // INSERT ALERT (ONE PER SNAPSHOT)
    // ----------------------
    $message = generateAlertMessage($detection_type, implode(", ", $labels));
    $status = "unread";

    $stmt = $conn->prepare("
        INSERT INTO alerts(
            device_id,
            detection_id,
            alert_type,
            message,
            status,
            created_at
        )
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "iisss",
        $device_id,
        $detection_id,
        $detection_type,
        $message,
        $status
    );

    $stmt->execute();
    $stmt->close();

    // ----------------------
    // RESPONSE
    // ----------------------
    echo json_encode([
        "status" => "success",
        "snapshot_id" => $snapshot_id,
        "total_objects" => count($labels)
    ]);

    // ----------------------
    // HELPERS
    // ----------------------
    function classifyCategory($label)
    {
        $label = strtolower($label);

        if (strpos($label, 'person') !== false)
            return 'Human';

        if (
            strpos($label, 'dog') !== false ||
            strpos($label, 'cat') !== false ||
            strpos($label, 'rat') !== false ||
            strpos($label, 'mouse') !== false
        )
            return 'Animal';

        if (strpos($label, 'bird') !== false)
            return 'Animal';

        return 'Object';
    }

    function generateAlertMessage($type, $objects)
    {
        switch ($type) {
            case 'Human':
                return "Human detected (" . $objects . ")";
            case 'Animal':
                return "Animals detected (" . $objects . ")";
            case 'Bird':
                return "Birds detected (" . $objects . ")";
            default:
                return "Objects detected (" . $objects . ")";
        }
    }
?>