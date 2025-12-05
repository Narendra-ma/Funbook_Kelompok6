<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['group_id'])) {
    echo json_encode(['success' => false, 'message' => 'Group ID required']);
    exit();
}

$group_id = intval($_GET['group_id']);

try {
    // Ambil anggota grup
    $sql = "SELECT u.id, u.username, gm.role 
            FROM users u 
            JOIN group_members gm ON u.id = gm.user_id 
            WHERE gm.group_id = ? 
            ORDER BY FIELD(gm.role, 'admin', 'moderator', 'member'), u.username 
            LIMIT 15";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$group_id]);
    $members = $stmt->fetchAll();

    $formatted_members = [];
    foreach ($members as $member) {
        $formatted_members[] = [
            'id' => $member['id'],
            'username' => htmlspecialchars($member['username']),
            'role' => $member['role']
        ];
    }

    echo json_encode([
        'success' => true,
        'members' => $formatted_members
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'members' => []
    ]);
}
?>