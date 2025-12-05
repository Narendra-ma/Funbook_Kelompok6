<?php
require_once 'config.php';

echo "<h2>Debug Database Connection</h2>";

// Cek koneksi
echo "<h3>1. Koneksi Database</h3>";
try {
    $conn->query("SELECT 1");
    echo "<p style='color:green'>✓ Koneksi database berhasil</p>";
} catch(Exception $e) {
    echo "<p style='color:red'>✗ Koneksi gagal: " . $e->getMessage() . "</p>";
}

// Cek tabel yang ada
echo "<h3>2. Tabel di Database</h3>";
$tables = ['users', 'groups', 'group_members', 'group_posts', 'group_post_likes', 'group_post_comments', 'posts'];

foreach($tables as $table) {
    try {
        $check = $conn->query("SELECT COUNT(*) as cnt FROM information_schema.tables 
                              WHERE table_schema = '" . DB_NAME . "' 
                              AND table_name = '$table'");
        $exists = $check->fetch()['cnt'] > 0;
        
        if($exists) {
            // Hitung jumlah data
            $count = $conn->query("SELECT COUNT(*) as total FROM $table")->fetch()['total'];
            echo "<p style='color:green'>✓ Table '$table' - $total rows</p>";
        } else {
            echo "<p style='color:red'>✗ Table '$table' tidak ada</p>";
        }
    } catch(Exception $e) {
        echo "<p style='color:orange'>? Table '$table' - Error: " . $e->getMessage() . "</p>";
    }
}

// Cek data di group_posts
echo "<h3>3. Data di group_posts</h3>";
try {
    $stmt = $conn->query("SELECT * FROM group_posts LIMIT 5");
    $posts = $stmt->fetchAll();
    
    if(count($posts) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Group ID</th><th>User ID</th><th>Content</th><th>Created</th></tr>";
        foreach($posts as $post) {
            echo "<tr>";
            echo "<td>" . $post['id'] . "</td>";
            echo "<td>" . $post['group_id'] . "</td>";
            echo "<td>" . $post['user_id'] . "</td>";
            echo "<td>" . substr($post['content'], 0, 50) . "...</td>";
            echo "<td>" . $post['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada data di group_posts</p>";
    }
} catch(Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Cek apakah user anggota grup
echo "<h3>4. Group Membership (User ID 1)</h3>";
try {
    $stmt = $conn->prepare("SELECT g.*, gm.role FROM groups g 
                           JOIN group_members gm ON g.id = gm.group_id 
                           WHERE gm.user_id = 1");
    $stmt->execute();
    $groups = $stmt->fetchAll();
    
    if(count($groups) > 0) {
        foreach($groups as $group) {
            echo "<p>✓ User 1 adalah anggota grup '" . $group['name'] . "' (Role: " . $group['role'] . ")</p>";
        }
    } else {
        echo "<p>User 1 bukan anggota grup apapun</p>";
    }
} catch(Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test insert ke group_posts
echo "<h3>5. Test Insert ke group_posts</h3>";
try {
    $test_content = "Test postingan dari debug script - " . date('Y-m-d H:i:s');
    $sql = "INSERT INTO group_posts (group_id, user_id, content) VALUES (1, 1, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$test_content])) {
        $last_id = $conn->lastInsertId();
        echo "<p style='color:green'>✓ Berhasil insert data (ID: $last_id)</p>";
        
        // Hapus data test
        $conn->exec("DELETE FROM group_posts WHERE id = $last_id");
        echo "<p>Data test telah dihapus</p>";
    } else {
        echo "<p style='color:red'>✗ Gagal insert data</p>";
    }
} catch(Exception $e) {
    echo "<p style='color:red'>✗ Error insert: " . $e->getMessage() . "</p>";
}
?>