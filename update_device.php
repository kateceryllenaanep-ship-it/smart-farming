<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}

require 'includes/connect.php';

// Validate POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device_id       = intval($_POST['device_id']);
    $component_name  = trim($_POST['component_name']);
    $component_type  = trim($_POST['component_type']);
    $esp32_pin       = intval($_POST['esp32_pin']);
    $esp32_uid       = trim($_POST['esp32_uid']);
    $status          = trim($_POST['status']);

    // Basic validation
    if (empty($component_name) || empty($component_type) || empty($esp32_uid)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: device_management.php");
        exit();
    }

    // Prepare SQL
    $stmt = $con->prepare("
        UPDATE devices
        SET device_name = ?, component_type = ?, esp32_pin = ?, esp32_uid = ?, status = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssissi", $component_name, $component_type, $esp32_pin, $esp32_uid, $status, $device_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Component updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update component: " . $stmt->error;
    }

    $stmt->close();
    header("Location: device_control.php");
    exit();
}
?>