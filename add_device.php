<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}

require 'includes/connect.php';

// Validate POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        INSERT INTO devices (device_name, component_type, esp32_pin, esp32_uid, status, created_date)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssiss", $component_name, $component_type, $esp32_pin, $esp32_uid, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Component added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add component: " . $stmt->error;
    }

    $stmt->close();
    header("Location: device_control.php");
    exit();
}
?>