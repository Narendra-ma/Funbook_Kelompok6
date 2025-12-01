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

    // Cek role user
    $sql = "SELECT role FROM group_members WHERE group_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$group_id, $user_id]);
    $member = $stmt->fetch();
    
    if($member){
        // Admin tidak bisa leave grup (harus delete grup atau transfer admin)
        if($member['role'] == 'admin'){
            $_SESSION['error'] = "Admin tidak bisa meninggalkan grup. Transfer admin terlebih dahulu atau hapus grup.";
        } else {
            // Leave grup
            $sql = "DELETE FROM group_members WHERE group_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            
            if($stmt->execute([$group_id, $user_id])){
                $_SESSION['success'] = "Berhasil keluar dari grup!";
            } else {
                $_SESSION['error'] = "Gagal keluar dari grup";
            }
        }
    } else {
        $_SESSION['error'] = "Anda bukan anggota grup ini";
    }
}

header("Location: ../groups.php");
exit;
?>