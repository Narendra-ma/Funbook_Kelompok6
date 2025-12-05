<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Cek login
if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

// Cek request method
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Ambil data dari POST
$user_id = $_SESSION['user_id'];
$group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validasi input
if(empty($content)){
    echo json_encode(['success' => false, 'message' => 'Konten tidak boleh kosong']);
    exit();
}

if($group_id === 0){
    echo json_encode(['success' => false, 'message' => 'Group ID tidak valid']);
    exit();
}

try {
    // Cek apakah user anggota grup
    $sql = "SELECT id, role FROM group_members WHERE group_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$group_id, $user_id]);
    
    if($stmt->rowCount() === 0){
        echo json_encode(['success' => false, 'message' => 'Anda bukan anggota grup ini']);
        exit();
    }
    
    $member = $stmt->fetch();
    
    // Simpan postingan ke group_posts
    $sql = "INSERT INTO group_posts (group_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$group_id, $user_id, $content])){
        $post_id = $conn->lastInsertId();
        
        // Ambil info user untuk response
        $sql_user = "SELECT username, profile_picture FROM users WHERE id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->execute([$user_id]);
        $user = $stmt_user->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Postingan berhasil dipublikasikan',
            'post_id' => $post_id,
            'username' => $user['username'],
            'profile_picture' => $user['profile_picture'] ?: 'default.jpg',
            'role' => $member['role'],
            'content' => $content
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan postingan']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>