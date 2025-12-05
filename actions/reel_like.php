<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$reel_id = $_POST['reel_id'];
$type = $_POST['type'];

$sql = "INSERT INTO reel_likes (user_id, reel_id, type) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE type = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id, $reel_id, $type, $type]);