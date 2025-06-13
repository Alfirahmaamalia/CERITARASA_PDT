<?php 
$title = "Masuk";
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
                    <a href="/auth/login" class="auth-nav-link active">Masuk</a>
                    <a href="/auth/register" class="auth-nav-link">Daftar</a>
                </div>
            </div>
            
            <div class="auth-form">
                <h2>Masuk ke akun</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <p><?= htmlspecialchars($success) ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/auth/login">
                    <div class="form-group">
                        <label for="username">Username atau Email</label>
                        <input type="text" id="username" name="username" placeholder="Masukan username atau email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" id="password" name="password" placeholder="Masukan Kata Sandi" required>
                    </div>
                    
                    <button type="submit" class="btn-primary">Masuk</button>
                    
                    <div class="auth-footer">
                        <a href="/auth/register">Belum punya akun?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>
