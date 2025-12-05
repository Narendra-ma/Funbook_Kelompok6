<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Cek parameter
if(!isset($_GET['group_id']) || empty($_GET['group_id'])){
    echo json_encode([
        'success' => false, 
        'message' => 'Parameter group_id diperlukan',
        'posts' => []
    ]);
    exit();
}

$group_id = intval($_GET['group_id']);
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

try {
    // Cek apakah group ada - GUNAKAN BACKTICKS
    $sql_check_group = "SELECT id FROM `groups` WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check_group);
    $stmt_check->execute([$group_id]);
    
    if($stmt_check->rowCount() === 0){
        echo json_encode([
            'success' => false, 
            'message' => 'Grup tidak ditemukan',
            'posts' => []
        ]);
        exit();
    }
    
    // Jika user login, cek apakah anggota grup
    if($user_id > 0){
        $sql_check_member = "SELECT role FROM group_members WHERE group_id = ? AND user_id = ?";
        $stmt_member = $conn->prepare($sql_check_member);
        $stmt_member->execute([$group_id, $user_id]);
        
        if($stmt_member->rowCount() === 0){
            echo json_encode([
                'success' => false, 
                'message' => 'Anda bukan anggota grup ini',
                'posts' => []
            ]);
            exit();
        }
    }
    
    // Query untuk mendapatkan postingan
    $sql = "SELECT gp.id, gp.group_id, gp.user_id, gp.content, gp.created_at,
                   u.username, u.profile_picture,
                   (SELECT COUNT(*) FROM group_post_likes WHERE post_id = gp.id) as like_count,
                   (SELECT COUNT(*) FROM group_post_comments WHERE post_id = gp.id) as comment_count";
    
    // Tambahkan check untuk like jika user login
    if($user_id > 0){
        $sql .= ", EXISTS(SELECT 1 FROM group_post_likes WHERE post_id = gp.id AND user_id = ?) as is_liked";
        $params = [$user_id, $group_id];
    } else {
        $sql .= ", 0 as is_liked";
        $params = [$group_id];
    }
    
    $sql .= " FROM group_posts gp 
             JOIN users u ON gp.user_id = u.id 
             WHERE gp.group_id = ? 
             ORDER BY gp.created_at DESC 
             LIMIT 20";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();
    
    // Format waktu
    function timeAgo($datetime) {
        if(empty($datetime)) return 'Baru saja';
        
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff/60) . ' menit lalu';
        if ($diff < 86400) return floor($diff/3600) . ' jam lalu';
        if ($diff < 604800) return floor($diff/86400) . ' hari lalu';
        
        return date('d M Y', $time);
    }
    
    $formatted_posts = [];
    foreach ($posts as $post) {
        $formatted_posts[] = [
            'id' => intval($post['id']),
            'content' => htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'),
            'username' => htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'),
            'profile_picture' => $post['profile_picture'] ?: 'default.jpg',
            'time_ago' => timeAgo($post['created_at']),
            'like_count' => intval($post['like_count']),
            'comment_count' => intval($post['comment_count']),
            'is_liked' => isset($post['is_liked']) ? (bool)$post['is_liked'] : false
        ];
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Berhasil mengambil postingan',
        'posts' => $formatted_posts,
        'total' => count($formatted_posts)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'posts' => []
    ]);
}
?>