<?php
session_start();
require_once __DIR__ . '/app/models/User.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: views/dashboard.php');
    exit();
}

$errors = [];

if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "Semua field harus diisi!";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Password tidak cocok!";
    }
    
    if (empty($errors)) {
        $userModel = new User();
        
        if ($userModel->findByUsername($username)) {
            $errors[] = "Username sudah digunakan!";
        }
        
        if ($userModel->findByEmail($email)) {
            $errors[] = "Email sudah digunakan!";
        }
        
        if (empty($errors)) {
            if ($userModel->register($username, $email, $password)) {
                header('Location: login.php?success=1');
                exit();
            } else {
                $errors[] = "Terjadi kesalahan saat registrasi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CeritaRasa</title>
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
                        <a href="login.php" class="auth-nav-link">Masuk</a>
                        <a href="register.php" class="auth-nav-link active">Daftar</a>
                    </div>
                </div>
                
                <div class="auth-form">
                    <h2>Buat akunmu</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <?php foreach ($errors as $error): ?>
                                <p><?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                                   placeholder="Masukan email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" 
                                   value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" 
                                   placeholder="Masukan username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Kata Sandi</label>
                            <input type="password" id="password" name="password" placeholder="Masukan Kata Sandi" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Kata Sandi</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Masukan Kata Sandi" required>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="terms" required>
                            <label for="terms">
                                Saya menyetujui <a href="#">Ketentuan Layanan</a> dan <a href="#">Kebijakan Privasi</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-primary">Selanjutnya</button>
                        
                        <div class="auth-footer">
                            <a href="login.php">Sudah punya akun</a>
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
