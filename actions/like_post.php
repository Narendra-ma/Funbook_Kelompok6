<?php
include '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    echo 'error';
    exit;
}

if($_POST['post_id']){
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    
    // Cek apakah sudah like
    $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $post_id]);
    
    if($stmt->rowCount() > 0){
        // Jika sudah like, hapus like (unlike)
        $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $post_id]);
        echo 'unliked';
    } else {
        // Jika belum like, tambah like
        $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $post_id]);
        echo 'liked';
    }
} else {
    echo 'error';
}
?>