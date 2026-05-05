<?php

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['image'])) {
    echo json_encode(["error" => "No image received"]);
    exit;
}

$imageBase64 = $data['image'];

// Remove base64 prefix
$imageBase64 = str_replace('data:image/jpeg;base64,', '', $imageBase64);

// Convert base64 to binary image
$imageData = base64_decode($imageBase64);

// Save temporarily
$tempFile = tempnam(sys_get_temp_dir(), 'img_') . '.jpg';
file_put_contents($tempFile, $imageData);

// Send to local YOLO server
$url = "http://127.0.0.1:8000/detect";

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, [
    'file' => new CURLFile($tempFile)
]);

$response = curl_exec($curl);

if ($response === false) {
    echo json_encode(["error" => curl_error($curl)]);
    curl_close($curl);
    unlink($tempFile);
    exit;
}

curl_close($curl);

// Delete temp file
unlink($tempFile);

// Decode YOLO response
$result = json_decode($response, true);

$detections = $result['detections'] ?? [];

$category = "unknown";

// Simple classification based on labels
foreach ($detections as $item) {

    $label = strtolower($item['label']);

    if (strpos($label, 'person') !== false) {
        $category = "human";
    } elseif (strpos($label, 'bird') !== false) {
        $category = "bird";
    } elseif (strpos($label, 'cat') !== false || strpos($label, 'dog') !== false) {
        $category = "animal";
    } elseif (strpos($label, 'rat') !== false || strpos($label, 'mouse') !== false) {
        $category = "rodent";
    } else {
        $category = "object";
    }
}

// Return to frontend
echo json_encode([
    "category" => $category,
    "detections" => $detections
]);