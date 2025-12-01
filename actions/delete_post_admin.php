<?php
include '../config.php';

// Cek login dan admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']){
    header("Location: ../login.php");
    exit;
}

if($_POST['post_id']){
    $post_id = $_POST['post_id'];
    
    // Hapus postingan
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$post_id])){
        $_SESSION['success'] = "Postingan berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus postingan";
    }
}

header("Location: ../admin.php");
exit;
?>