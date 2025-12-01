<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$caption = $_POST['caption'] ?? '';
$is_meme = isset($_POST['is_meme']) ? 1 : 0;
$error = null;

// Handle photo upload
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    // Validasi ukuran
    if ($_FILES['photo']['size'] > MAX_IMAGE_SIZE) {
        $error = "Ukuran gambar terlalu besar. Maksimal 5MB.";
    }
    
    // Validasi ekstensi
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    global $allowed_image_types;
    
    if (!in_array($ext, $allowed_image_types)) {
        $error = "Format gambar tidak didukung. Gunakan: " . implode(', ', $allowed_image_types);
    }
    
    if (!$error) {
        // Generate unique filename
        $new_filename = time() . '_' . uniqid() . '.' . $ext;
        
        // Tentukan direktori berdasarkan tipe
        if ($is_meme) {
            $upload_dir = '../assets/memes/';
            $relative_path = 'memes/' . $new_filename;
            $content_type = 'meme';
        } else {
            $upload_dir = '../assets/images/posts/';
            $relative_path = 'images/posts/' . $new_filename;
            $content_type = 'post';
        }
        
        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Upload file
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $new_filename)) {
            // Simpan ke tabel posts
            $sql = "INSERT INTO posts (user_id, content, image, content_type) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $caption, $relative_path, $content_type]);
            
            $_SESSION['success'] = "Foto berhasil diupload!";
        } else {
            $error = "Gagal mengupload foto.";
        }
    }
} else {
    $error = "Silakan pilih foto.";
}

if ($error) {
    $_SESSION['error'] = $error;
}

// Redirect berdasarkan tipe
if ($is_meme) {
    header("Location: ../meme.php");
} else {
    header("Location: ../index.php");
}
exit;
?>