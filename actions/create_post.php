<?php
session_start();
require_once '../config.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if(isset($_POST['content'])){
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];
    $image = null;

    // Handle upload gambar
    if(isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0){
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['post_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($filetype, $allowed)){
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = '../assets/images/posts/' . $new_filename;
            
            if(move_uploaded_file($_FILES['post_image']['tmp_name'], $upload_path)){
                $image = $new_filename;
            }
        }
    }

    // Simpan postingan ke tabel posts (untuk homepage)
    $sql = "INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$user_id, $content, $image])){
        $_SESSION['success'] = "Postingan berhasil dipublikasikan!";
    } else {
        $_SESSION['error'] = "Gagal membuat postingan";
    }
}

// Redirect ke homepage, BUKAN groups.php
header("Location: ../index.php");
exit;
?>