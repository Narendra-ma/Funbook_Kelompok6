<?php
include 'config.php';

// Cek apakah user sudah login
if(!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    header("Location: login.php");
    exit;
}

// Ambil data user saat ini jika sudah login
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $current_user = $stmt->fetch();
    
    if(!$current_user) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FunBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open me-2"></i>FunBook
            </a>
            <div class="d-none d-lg-flex mx-4 flex-grow-1">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-0 bg-light" placeholder="Cari di FunBook...">
                </div>
            </div>
            <div class="d-flex align-items-center">
                <!-- Home -->
                <a href="index.php" class="btn btn-light btn-sm mx-1 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home fa-lg"></i>
                </a>
                
                <!-- Friends -->
                <a href="friends.php" class="btn btn-light btn-sm mx-1">
                    <i class="fas fa-user-friends fa-lg"></i>
                </a>
                
                <!-- Reels -->
                <a href="reels.php" class="btn btn-light btn-sm mx-1 <?php echo basename($_SERVER['PHP_SELF']) == 'reels.php' ? 'active' : ''; ?>">
                    <i class="fas fa-play-circle fa-lg"></i>
                </a>
                
                <!-- Meme -->
                <a href="meme.php" class="btn btn-light btn-sm mx-1 <?php echo basename($_SERVER['PHP_SELF']) == 'meme.php' ? 'active' : ''; ?>">
                    <i class="fas fa-gamepad fa-lg"></i>
                </a>
                
                <!-- Notifications -->
                <div class="dropdown mx-1">
                    <button class="btn btn-light btn-sm position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Notifikasi</h6>
                        <a class="dropdown-item" href="#">
                            <small>Anda mendapat like baru</small>
                        </a>
                        <a class="dropdown-item" href="#">
                            <small>User1 mengomentari postingan Anda</small>
                        </a>
                    </div>
                </div>
                
                <!-- User Profile Dropdown -->
                <div class="dropdown ms-2">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <img src="assets/images/profiles/<?php echo $current_user['profile_picture']; ?>" 
                             class="rounded-circle me-2" width="32" height="32">
                        <span class="d-none d-md-inline"><?php echo explode(' ', $current_user['full_name'])[0]; ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profil Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="profile.php?id=<?php echo $user_id; ?>">
                                <i class="fas fa-edit me-2"></i>Edit Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if($current_user['is_admin']): ?>
                        <li>
                            <a class="dropdown-item" href="admin.php">
                                <i class="fas fa-cog me-2"></i>Admin Panel
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>