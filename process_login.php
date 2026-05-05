<?php  
session_start();
require 'includes/connect.php';

if (isset($_POST['login'])) {  

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    
    $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows === 0) {
        header("Location: login.php?login=notfound");
        exit();
    }

    $user = $result->fetch_assoc();

    
    if (!($password === $user['password'] || password_verify($password, $user['password']))) {
        header("Location: login.php?login=wrongpass");
        exit();
    }

    
    $_SESSION['user_id'] = $user['id'];  
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_type'] = $user['role'];

    header("Location: dashboard.php?login=success");
    exit();
}
?>