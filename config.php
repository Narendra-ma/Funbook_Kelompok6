<?php
// Koneksi database
$host = "localhost";
$user = "root"; 
$pass = "";
$dbname = "funbook";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Mulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'funbook');

// Path configuration
define('BASE_URL', 'http://localhost/funbook/');
define('UPLOAD_PATH', __DIR__ . '/assets/');

// File size limits (in bytes)
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_VIDEO_SIZE', 50 * 1024 * 1024); // 50MB

// Allowed file types
$allowed_image_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$allowed_video_types = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];

// Database connection
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in (for protected pages)
function requireLogin() {
    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Check if user is admin
function requireAdmin() {
    if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header("Location: index.php");
        exit;
    }
}
?>