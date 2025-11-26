<?php
session_start();
if(isset($_SESSION['username'])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Funbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="container container-fluid">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger mt-3" role="alert"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success mt-3" role="alert"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <form method="POST" action="register_action.php">
            <div class="card login-card">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Daftar Funbook</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label for="full_name" class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="username" class="col-sm-3 col-form-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="password" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="confirm_password" class="col-sm-3 col-form-label">Konfirmasi Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary me-2">Daftar</button>
                    <a href="login.php" class="btn btn-outline-primary">Login</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>