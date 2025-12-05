<?php
include '../config.php';

if($_POST['username'] && $_POST['email'] && $_POST['password'] && $_POST['full_name']){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];

    // Validasi
    if($password !== $confirm_password){
        $_SESSION['error'] = "Password dan konfirmasi password tidak cocok";
        header("Location: ../register.php");
        exit;
    }

    if(strlen($password) < 6){
        $_SESSION['error'] = "Password minimal 6 karakter";
        header("Location: ../register.php");
        exit;
    }

    // Cek apakah username atau email sudah ada
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $email]);

    if($stmt->rowCount() > 0){
        $_SESSION['error'] = "Username atau email sudah digunakan";
        header("Location: ../register.php");
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Simpan user baru
    $sql = "INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$username, $email, $hashed_password, $full_name])){
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: ../login.php");
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat registrasi";
        header("Location: ../register.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Semua field harus diisi";
    header("Location: ../register.php");
    exit;
}
?>