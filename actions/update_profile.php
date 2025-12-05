<?php
session_start();
include '../connection.php';
include '../class/User.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if($_POST){
    $user_id = $_SESSION['user_id'];
    $bio = $_POST['bio'];
    
    $user = new User($conn);
    $user->id = $user_id;
    
    // Handle profile picture upload
    $profile_picture = null;
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $profile_picture = uploadImage($_FILES['profile_picture'], 'profiles');
    }
    
    // Handle cover photo upload
    $cover_photo = null;
    if(isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === 0) {
        $cover_photo = uploadImage($_FILES['cover_photo'], 'covers');
    }
    
    if($user->updateProfile($bio, $profile_picture, $cover_photo)) {
        $_SESSION['success'] = "Profil berhasil diperbarui";
    } else {
        $_SESSION['error'] = "Gagal memperbarui profil";
    }
    
    header("Location: ../profile.php");
    exit;
}

function uploadImage($file, $type) {
    $target_dir = "../assets/images/" . $type . "/";
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