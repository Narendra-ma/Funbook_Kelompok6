<?php
include '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if($_POST['group_name']){
    $name = $_POST['group_name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Buat grup baru
    $sql = "INSERT INTO groups (name, description, created_by) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$name, $description, $user_id])){
        $group_id = $conn->lastInsertId();
        
        // Tambahkan pembuat sebagai admin
        $sql = "INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'admin')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$group_id, $user_id]);
        
        $_SESSION['success'] = "Grup berhasil dibuat!";
    } else {
        $_SESSION['error'] = "Gagal membuat grup";
    }
}

header("Location: ../groups.php");
exit;
?>