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

    // Hapus pertemanan
    $sql = "DELETE FROM friends 
            WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$user_id, $friend_id, $friend_id, $user_id])){
        $_SESSION['success'] = "Teman berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus teman";
    }
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../friends.php'));
exit;
?>