<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}