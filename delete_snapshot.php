<?php
require 'includes/connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ----------------------
    // GET IMAGE PATH FIRST
    // ----------------------
    $query = "SELECT image_path FROM snapshots WHERE id = ?";
    $stmt = $con->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    // ----------------------
    // DELETE RELATED DETECTIONS
    // ----------------------
    $stmt = $con->prepare("DELETE FROM detections WHERE snapshot_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // ----------------------
    // DELETE SNAPSHOT RECORD
    // ----------------------
    $stmt = $con->prepare("DELETE FROM snapshots WHERE id = ?");
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        die("Delete failed: " . $stmt->error);
    }

    $stmt->close();

    // ----------------------
    // DELETE IMAGE FILE
    // ----------------------
    if (!empty($imagePath) && file_exists($imagePath)) {
        unlink($imagePath);
    }

    // ----------------------
    // REDIRECT
    // ----------------------
    header("Location: snapshots.php");
    exit();
}
?>