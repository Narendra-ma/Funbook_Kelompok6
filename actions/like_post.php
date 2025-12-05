<?php
session_start();
include '../connection.php';
include '../class/Post.php';

if(isset($_POST['post_id']) && isset($_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    
    if(Post::like($conn, $user_id, $post_id)) {
        // Check if now liked or unliked
        if(Post::isLiked($conn, $user_id, $post_id)) {
            echo 'liked';
        } else {
            echo 'unliked';
        }
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>