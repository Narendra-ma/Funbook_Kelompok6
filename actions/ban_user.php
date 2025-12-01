<?php
include '../config.php';

// Cek apakah admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']){
    header("Location: ../login.php");
    exit;
}

if($_POST['user_id']){
    $target_user_id = $_POST['user_id'];
    
    // Cek apakah target user adalah admin
    $sql = "SELECT is_admin FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$target_user_id]);
    $target_user = $stmt->fetch();
    
    if($target_user['is_admin']){
        $_SESSION['error'] = "Tidak bisa ban user yang berstatus admin!";
    } else {
        // Ban user
        $sql = "UPDATE users SET is_banned = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if($stmt->execute([$target_user_id])){
            $_SESSION['success'] = "User berhasil dibanned!";
        } else {
            $_SESSION['error'] = "Gagal membanned user";
        }
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