<?php
include '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    echo 'error';
    exit;
}

if($_POST['user_id']){
    $friend_id = $_POST['user_id'];
    $user_id = $_SESSION['user_id'];

    // Cek apakah sudah ada permintaan pertemanan
    $sql = "SELECT id FROM friends 
            WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
    
    if($stmt->rowCount() == 0){
        // Kirim permintaan pertemanan
        $sql = "INSERT INTO friends (user_id1, user_id2, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        
        if($stmt->execute([$user_id, $friend_id])){
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'already_exists';
    }
} else {
    echo 'error';
}
?>