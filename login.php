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
    <title>Login - Funbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="container container-fluid">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger mt-3" role="alert"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form method="POST" action="login_action.php">
            <div class="card login-card">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Login Funbook</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label for="username" class="col-sm-3 col-form-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="password" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary me-2">Login</button>
                    <a href="register.php" class="btn btn-outline-primary">Register</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>