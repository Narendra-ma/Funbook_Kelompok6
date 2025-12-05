<?php
session_start();
include '../connection.php';
include '../class/Friend.php';

if(isset($_POST['user_id']) && isset($_SESSION['user_id'])) {
    $friend_id = $_POST['user_id'];
    $user_id = $_SESSION['user_id'];

    if(Friend::sendRequest($conn, $user_id, $friend_id)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>