<?php
include 'config.php';

// Jika sudah login, redirect ke index
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Funbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card p-4">
                    <div class="text-center mb-4">
                        <h3 class="text-primary">
                            <i class="fas fa-gamepad me-2"></i>Funbook
                        </h3>
                        <p class="text-muted">Login ke akun Anda</p>
                    </div>

                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="actions/login_action.php">
                        <div class="mb-3">
                            <label class="form-label">Username atau Email</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                    </form>

                    <div class="text-center">
                        <a href="register.php" class="text-decoration-none">Belum punya akun? Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>