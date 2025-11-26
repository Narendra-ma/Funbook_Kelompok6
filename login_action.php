<?php
session_start();
include 'class/User.php';
include 'connection.php';

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = User::find($conn, $username, $password);

    if($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['full_name'] = $user->full_name;
        $_SESSION['is_admin'] = $user->is_admin;
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Invalid username or password";
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>