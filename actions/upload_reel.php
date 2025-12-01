<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$caption = $_POST['caption'];
$user_id = $_SESSION['user_id'];

$video = null;

if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {

    $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));

    if ($ext != 'mp4') {
        die("Invalid file type");
    }

    $new_file = time() . "_" . $_FILES['video']['name'];
    $upload_path = "../assets/reels/" . $new_file;

    if (move_uploaded_file($_FILES['video']['tmp_name'], $upload_path)) {
        $video = $new_file;
    }
}

$sql = "INSERT INTO reels (user_id, video, caption) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id, $video, $caption]);

header("Location: ../reels.php");
exit;
