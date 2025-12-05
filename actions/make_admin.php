<?php
include '../config.php';

// Cek apakah admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']){
    header("Location: ../login.php");
    exit;
}

if($_POST['user_id']){
    $target_user_id = $_POST['user_id'];
    
    // Update user menjadi admin
    $sql = "UPDATE users SET is_admin = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$target_user_id])){
        $_SESSION['success'] = "User berhasil dijadikan admin!";
    } else {
        $_SESSION['error'] = "Gagal menjadikan user sebagai admin";
    }
}

// Redirect berdasarkan parameter
if(isset($_POST['redirect']) && $_POST['redirect'] == 'profile' && isset($_POST['profile_id'])){
    header("Location: ../profile.php?id=" . $_POST['profile_id']);
} else {
    header("Location: ../admin.php");
}
exit;
?>