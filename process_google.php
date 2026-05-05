<?php
    session_start();
    header('Content-Type: application/json');
    include 'includes/connect.php';
    require 'vendor/autoload.php';

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
        exit;
    }

    $jwt = $data['credential'] ?? '';

    if (!$jwt) {
        echo json_encode(['status' => 'error', 'message' => 'No credential received']);
        exit;
    }

    $client = new Google_Client(['client_id' => '731537082526-d2o9tff74ep446buij7l1fdqt76m3mqb.apps.googleusercontent.com']);
    try {
        $payload = $client->verifyIdToken($jwt);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Google verification failed: ' . $e->getMessage()]);
        exit;
    }

    if ($payload) {
        $email = $payload['email'];
        $name = $payload['name'];
        
        $stmt = $con->prepare("SELECT id, role FROM users WHERE username=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $user = $result->fetch_assoc();
        } else {
            $hashed_password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $role = 'user';
            $stmt = $con->prepare("INSERT INTO users (name, username, password, role) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            $stmt->execute();
            $user = ['id'=>$con->insert_id,'role'=>'user'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $email;
        $_SESSION['user_type'] = $user['role'];

        echo json_encode(['status'=>'success']);
        exit;
    } else {
        echo json_encode(['status'=>'error','message'=>'Invalid Google token']);
        exit;
    }