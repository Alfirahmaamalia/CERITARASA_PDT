<?php
session_start();
require_once __DIR__ . '/app/models/User.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: views/dashboard.php');
    exit();
}

$error = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $userModel = new User();
        $user = $userModel->login($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            header('Location: views/dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CeritaRasa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <img src="fotoutama.png" alt="Food Background" class="auth-bg-image">
        </div>
        
        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-header">
                    <div class="auth-nav">
                        <a href="login.php" class="auth-nav-link active">Masuk</a>
                        <a href="register.php" class="auth-nav-link">Daftar</a>
                    </div>
                </div>
                
                <div class="auth-form">
                    <h2>Masuk ke akun</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <p><?= htmlspecialchars($error) ?></p>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username">Username atau Email</label>
                            <input type="text" id="username" name="username" 
                                   value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" 
                                   placeholder="Masukan username atau email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Kata Sandi</label>
                            <input type="password" id="password" name="password" placeholder="Masukan Kata Sandi" required>
                        </div>
                        
                        <button type="submit" class="btn-primary">Masuk</button>
                        
                        <div class="auth-footer">
                            <a href="register.php">Belum punya akun?</a>
                            <br>
                            <a href="index.php">Kembali ke Beranda</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
