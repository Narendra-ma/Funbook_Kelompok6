<?php
include '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if($_POST['friend_id']){
    $friend_id = $_POST['friend_id'];
    $user_id = $_SESSION['user_id'];

    // Hapus permintaan pertemanan
    $sql = "DELETE FROM friends 
            WHERE user_id1 = ? AND user_id2 = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$friend_id, $user_id])){
        $_SESSION['success'] = "Permintaan pertemanan berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus permintaan pertemanan";
    }
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../friends.php'));
exit;
?>