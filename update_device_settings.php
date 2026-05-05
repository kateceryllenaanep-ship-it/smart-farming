<?php
require 'includes/connect.php';

if($_POST){

    $device_id = $_POST['device_id'];
    $pir = $_POST['pir_sensitivity'];
    $res = $_POST['camera_resolution'];
    $buzzer = $_POST['buzzer_duration'];
    $led = $_POST['led_mode'];

    mysqli_query($con, "
        UPDATE device_settings SET
        pir_sensitivity='$pir',
        camera_resolution='$res',
        buzzer_duration='$buzzer',
        led_mode='$led'
        WHERE device_id='$device_id'
    ");

    header("Location: device_control.php?status=updated");
    exit();
}