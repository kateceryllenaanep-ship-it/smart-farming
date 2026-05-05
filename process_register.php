<?php
session_start();
include 'includes/connect.php';

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ✅ 4. Check if email already exists
    $check = $con->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: login.php?register=exists");
        exit();
    }

    // ✅ 5. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ 6. Insert user
    $stmt = $con->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: login.php?register=registered");
    } else {
        header("Location: login.php?register=error");
    }

    exit();
}