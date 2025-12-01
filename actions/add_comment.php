<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if($_POST['post_id'] && $_POST['content']){
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    // Cek apakah user dibanned
    $sql = "SELECT is_banned FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if($user['is_banned']){
        $_SESSION['error'] = "Akun Anda telah dibanned!";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // Insert komentar
    $sql = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$post_id, $user_id, $content])){
        $_SESSION['success'] = "Komentar berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan komentar";
    }
}

// Redirect kembali ke halaman komentar
if(isset($_POST['post_id'])){
    header("Location: ../comment.php?post_id=" . $_POST['post_id']);
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
exit;
?>