<?php
include '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    echo 'error';
    exit;
}

if($_POST['friend_id']){
    $friend_id = $_POST['friend_id'];
    $user_id = $_SESSION['user_id'];

    // Terima permintaan pertemanan
    $sql = "UPDATE friends SET status = 'accepted' 
            WHERE user_id1 = ? AND user_id2 = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$friend_id, $user_id])){
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>