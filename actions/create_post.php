<?php
session_start();
include '../connection.php';
include '../class/Post.php';

if(isset($_POST['content']) && isset($_SESSION['user_id'])) {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    // Handle image upload
    $image = null;
    if(isset($_FILES['post_image']) && $_FILES['post_image']['error'] === 0) {
        $image = uploadImage($_FILES['post_image']);
    }
    
    if(Post::create($conn, $user_id, $content, $image)) {
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal membuat postingan";
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}

function uploadImage($file) {
    $target_dir = "../assets/images/posts/";
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $filename;
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return null;
    }
    
    // Check file size (5MB)
    if($file["size"] > 5000000) {
        return null;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return null;
    }
    
    if(move_uploaded_file($file["tmp_name"], $target_file)) {
        return $filename;
    }
    
    return null;
}
?>