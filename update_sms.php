<?php
require 'includes/connect.php';

$phone = $_POST['phone'];
$sender = $_POST['sender'];
$api = $_POST['api_key'];

// SIMPLE UPSERT
$check = mysqli_query($con, "SELECT * FROM sms_settings LIMIT 1");

if(mysqli_num_rows($check) > 0){
    mysqli_query($con, "
        UPDATE sms_settings 
        SET phone_number='$phone', sender_name='$sender', api_key='$api'
    ");
} else {
    mysqli_query($con, "
        INSERT INTO sms_settings (phone_number, sender_name, api_key)
        VALUES ('$phone', '$sender', '$api')
    ");
}

header("Location: settings.php");

