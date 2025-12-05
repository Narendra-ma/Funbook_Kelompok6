<?php
include '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if($_POST['group_id']){
    $group_id = $_POST['group_id'];
    $user_id = $_SESSION['user_id'];

    // Cek apakah sudah menjadi anggota
    $sql = "SELECT id FROM group_members WHERE group_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$group_id, $user_id]);
    
    if($stmt->rowCount() == 0){
        // Gabung grup
        $sql = "INSERT INTO group_members (group_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        
        if($stmt->execute([$group_id, $user_id])){
            $_SESSION['success'] = "Berhasil bergabung dengan grup!";
        } else {
            $_SESSION['error'] = "Gagal bergabung dengan grup";
        }
    } else {
        $_SESSION['error'] = "Anda sudah menjadi anggota grup ini";
    }
}

header("Location: ../groups.php");
exit;
?>