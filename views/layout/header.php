<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>CeritaRasa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <?php if (!isset($hide_nav) || !$hide_nav): ?>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/">CeritaRasa</a>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-menu">
                <a href="/" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="/saved" class="nav-link">
                    <i class="fas fa-bookmark"></i> Saved Recipes
                </a>
                <a href="/resep/create" class="nav-link">
                    <i class="fas fa-plus"></i> Submit Recipe
                </a>
                <div class="nav-profile">
                    <i class="fas fa-user"></i> Profile
                    <div class="dropdown">
                        <a href="/resep/my">My Recipes</a>
                        <a href="/auth/logout">Logout</a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="nav-menu">
                <a href="/auth/login" class="nav-link">Masuk</a>
                <a href="/auth/register" class="btn-register">Daftar</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    <?php endif; ?>
