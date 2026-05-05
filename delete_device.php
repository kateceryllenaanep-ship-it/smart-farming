<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: agri_error.php?type=unauthorized");
        exit();
    }

    require 'includes/connect.php';

    // Check if device ID is provided
    if (isset($_GET['id'])) {
        $device_id = intval($_GET['id']);

        // Prepare delete statement
        $stmt = $con->prepare("DELETE FROM devices WHERE id = ?");
        $stmt->bind_param("i", $device_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Component deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete component: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid component ID.";
    }

    // Redirect back to device management page
    header("Location: device_management.php");
    exit();
?>