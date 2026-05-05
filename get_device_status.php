<?php
session_start();
require 'includes/connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$uids = $input['uids'] ?? [];

$response = [];
if(!empty($uids)) {
    $placeholders = implode(',', array_fill(0, count($uids), '?'));
    $types = str_repeat('s', count($uids));
    $stmt = $con->prepare("SELECT esp32_uid, status FROM devices WHERE esp32_uid IN ($placeholders)");
    $stmt->bind_param($types, ...$uids);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) {
        $status = $row['status'] === 'active' ? 'online' : $row['status'];
        $response[$row['esp32_uid']] = $status;
    }
}
echo json_encode($response);