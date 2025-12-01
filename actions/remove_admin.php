<?php
include '../config.php';

// Cek apakah admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']){
    header("Location: ../login.php");
    exit;
}

if($_POST['user_id']){
    $target_user_id = $_POST['user_id'];
    $current_admin_id = $_SESSION['user_id'];
    
    // Cek apakah admin mencoba menghapus dirinya sendiri
    if($target_user_id == $current_admin_id){
        $_SESSION['error'] = "Anda tidak dapat menghapus hak admin dari diri sendiri!";
    } else {
        // Update user menjadi bukan admin
        $sql = "UPDATE users SET is_admin = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if($stmt->execute([$target_user_id])){
            $_SESSION['success'] = "Hak admin berhasil dihapus dari user!";
        } else {
            $_SESSION['error'] = "Gagal menghapus hak admin";
        }
    }
}

header("Location: ../admin.php");
exit;
?>