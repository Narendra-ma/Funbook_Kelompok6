<?php
include '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if($_POST){
    $user_id = $_SESSION['user_id'];
    $bio = $_POST['bio'];
    $profile_picture = null;
    $cover_photo = null;

    // Handle upload foto profil
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0){
        $file = $_FILES['profile_picture'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($file_ext, $allowed_ext) && $file['size'] <= 5000000){
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_path = '../assets/images/profiles/' . $new_file_name;
            
            if(move_uploaded_file($file['tmp_name'], $upload_path)){
                $profile_picture = $new_file_name;
            }
        }
    }

    // Handle upload foto cover
    if(isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] == 0){
        $file = $_FILES['cover_photo'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($file_ext, $allowed_ext) && $file['size'] <= 5000000){
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_path = '../assets/images/covers/' . $new_file_name;
            
            if(move_uploaded_file($file['tmp_name'], $upload_path)){
                $cover_photo = $new_file_name;
            }
        }
    }

    // Update database
    if($profile_picture && $cover_photo){
        $sql = "UPDATE users SET bio = ?, profile_picture = ?, cover_photo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$bio, $profile_picture, $cover_photo, $user_id]);
    } elseif($profile_picture){
        $sql = "UPDATE users SET bio = ?, profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$bio, $profile_picture, $user_id]);
    } elseif($cover_photo){
        $sql = "UPDATE users SET bio = ?, cover_photo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$bio, $cover_photo, $user_id]);
    } else {
        $sql = "UPDATE users SET bio = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$bio, $user_id]);
    }

    $_SESSION['success'] = "Profil berhasil diperbarui!";
    header("Location: ../profile.php");
    exit;
}
?>