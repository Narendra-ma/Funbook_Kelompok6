<?php
include '../config.php';

if($_POST['username'] && $_POST['password']){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cari user di database
    $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND is_banned = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    // Cek password
    if($user && password_verify($password, $user['password'])){
        // Login berhasil
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: ../index.php");
        exit;
    } else {
        // Login gagal
        $_SESSION['error'] = "Username atau password salah";
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>