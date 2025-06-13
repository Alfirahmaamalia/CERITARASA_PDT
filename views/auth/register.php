<?php 
$title = "Daftar";
$hide_nav = true;
include '../views/layout/header.php'; 
?>

<div class="auth-container">
    <div class="auth-left">
        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Sign%20Up-fUutm5qZkKIoqdyDiVgjXiocI02zOw.png" alt="Food Background" class="auth-bg-image">
    </div>
    
    <div class="auth-right">
        <div class="auth-form-container">
            <div class="auth-header">
                <div class="auth-nav">
                    <a href="/auth/login" class="auth-nav-link">Masuk</a>
                    <a href="/auth/register" class="auth-nav-link active">Daftar</a>
                </div>
            </div>
            
            <div class="auth-form">
                <h2>Buat akunmu</h2>
                
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/auth/register">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukan email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukan username" required>
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
                        <a href="/auth/login">Sudah punya akun</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>
