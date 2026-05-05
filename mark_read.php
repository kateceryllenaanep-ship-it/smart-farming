<?php
    require 'includes/connect.php';

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        mysqli_query($con, "UPDATE alerts SET status='read' WHERE id=$id");
    }
?>