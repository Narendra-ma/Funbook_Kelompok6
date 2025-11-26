<?php
session_start();
include 'class/User.php';
include 'connection.php';

if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['full_name'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];

    // Validasi
    if($password !== $confirm_password){
        $_SESSION['error'] = "Password dan konfirmasi password tidak cocok";
        header("Location: register.php");
        exit;
    }

    if(strlen($password) < 6){
        $_SESSION['error'] = "Password minimal 6 karakter";
        header("Location: register.php");
        exit;
    }

    // Cek apakah username atau email sudah ada
    $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bindParam(1, $username);
    $check_stmt->bindParam(2, $email);
    $check_stmt->execute();

    if($check_stmt->rowCount() > 0){
        $_SESSION['error'] = "Username atau email sudah digunakan";
        header("Location: register.php");
        exit;
    }

    // Registrasi user
    if(User::register($conn, $username, $email, $password, $full_name)){
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat registrasi";
        header("Location: register.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Semua field harus diisi";
    header("Location: register.php");
    exit;
}
?>