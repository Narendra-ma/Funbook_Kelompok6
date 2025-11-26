<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
include 'connection.php';
include 'class/User.php';

$current_user = User::getById($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php if(isset($_SESSION['username'])): ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="fas fa-gamepad me-2"></i>Funbook
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reels.php">Reels</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="meme.php">Meme</a>
                    </li>
                    <?php if($_SESSION['is_admin']): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Admin</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex" role="logout" action="logout.php" method="POST">
                    <span class="navbar-text me-3">
                        <img src="assets/images/profiles/<?php echo $current_user['profile_picture']; ?>" 
                             class="profile-img-sm me-2">
                        <?php echo $current_user['full_name']; ?>
                    </span>
                    <button class="btn btn-outline-danger" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    <div class="container-fluid mt-5 pt-4">