<?php
require 'includes/connect.php';

$id = $_POST['id'];
$name = $_POST['name'];
$username = $_POST['username'];
$password = $_POST['password'];

if(!empty($password)){
    $password = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($con, "UPDATE users SET name='$name', username='$username', password='$password' WHERE id='$id'");
} else {
    mysqli_query($con, "UPDATE users SET name='$name', username='$username' WHERE id='$id'");
}

header("Location: settings.php");