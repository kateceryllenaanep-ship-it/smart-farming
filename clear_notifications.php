<?php
    require 'includes/connect.php';
    mysqli_query($con, "UPDATE alerts SET status='read'");
?>