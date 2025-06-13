<?php
session_start();
require_once __DIR__ . '/app/models/Resep.php';

$resepModel = new Resep();
$latest_reseps = $resepModel->getAllWithUser();
// Ambil 6 resep terbaru
$latest_reseps = array_slice($latest_reseps, 0, 6);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CeritaRasa - Berbagi Resep Masakan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php">CeritaRasa</a>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="views/saved.php" class="nav-link">
                    <i class="fas fa-bookmark"></i> Saved Recipes
                </a>
                <a href="views/create_recipe.php" class="nav-link">
                    <i class="fas fa-plus"></i> Submit Recipe
                </a>
                <div class="nav-profile">
                    <i class="fas fa-user"></i> Profile
                    <div class="dropdown">
                        <a href="views/my_recipes.php">My Recipes</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="nav-menu">
                <a href="login.php" class="nav-link">Masuk</a>
                <a href="register.php" class="btn-register">Daftar</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-content">
                <h3>Filters</h3>
                
                <div class="filter-section">
                    <h4>Cuisine Type</h4>
                    <div class="filter-options">
                        <label><input type="checkbox" value="Indonesian"> Indonesian</label>
                        <label><input type="checkbox" value="Italian"> Italian</label>
                        <label><input type="checkbox" value="Asian"> Asian</label>
                        <label><input type="checkbox" value="Mediterranean"> Mediterranean</label>
                    </div>
                </div>
                
                <div class="filter-section">
                    <h4>Main Ingredients</h4>
                    <div class="filter-options">
                        <label><input type="checkbox" value="Chicken"> Chicken</label>
                        <label><input type="checkbox" value="Beef"> Beef</label>
                        <label><input type="checkbox" value="Seafood"> Seafood</label>
                        <label><input type="checkbox" value="Vegetarian"> Vegetarian</label>
                    </div>
                </div>
                
                <div class="filter-section">
                    <h4>Difficulty Level</h4>
                    <div class="filter-options">
                        <label><input type="checkbox" value="Easy"> Easy</label>
                        <label><input type="checkbox" value="Medium"> Medium</label>
                        <label><input type="checkbox" value="Hard"> Hard</label>
                    </div>
                </div>
                
                <button class="btn-filter">Apply Filters</button>
            </div>
        </div>
        
        <div class="main-content">
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="welcome-banner">
                <div class="welcome-content">
                    <div class="welcome-avatar">
                        <img src="public/images/avatar-placeholder.png" alt="User Avatar">
                    </div>
                    <div class="welcome-text">
                        <h2>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
                        <p>Ready to discover delicious new recipes today?</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="search-section">
                <div class="search-container">
                    <input type="text" placeholder="Search for recipes, ingredients, or cuisine..." class="search-input">
                    <button class="search-btn">Search</button>
                </div>
            </div>
            
            <div class="recipes-grid">
                <?php if (!empty($latest_reseps)): ?>
                    <?php foreach ($latest_reseps as $resep): ?>
                    <div class="recipe-card" 
                         data-difficulty="<?= $resep['difficulty_level'] ?>"
                         data-cuisine="<?= $resep['cuisine_type'] ?>"
                         data-cooking-time="<?= $resep['cooking_time'] ?>">
                        <div class="recipe-image">
                            <img src="<?= $resep['image_url'] ? $resep['image_url'] : '/placeholder.svg?height=200&width=300' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>">
                            <div class="recipe-difficulty <?= strtolower($resep['difficulty_level']) ?>">
                                <?= htmlspecialchars($resep['difficulty_level']) ?>
                            </div>
                        </div>
                        <div class="recipe-content">
                            <h3><?= htmlspecialchars($resep['judul']) ?></h3>
                            <p class="recipe-author">By <?= htmlspecialchars($resep['username']) ?></p>
                            <p class="recipe-description"><?= htmlspecialchars(substr($resep['deskripsi'], 0, 100)) ?>...</p>
                            <div class="recipe-meta">
                                <span><i class="fas fa-clock"></i> <?= $resep['cooking_time'] ?> min</span>
                                <span><i class="fas fa-globe"></i> <?= $resep['cuisine_type'] ?></span>
                                <span><i class="fas fa-signal"></i> <?= $resep['difficulty_level'] ?></span>
                            </div>
                            <a href="views/recipe_detail.php?id=<?= $resep['id'] ?>" class="btn-view-recipe">View Recipe</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Belum ada resep tersedia.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 CeritaRasa. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/js/script.js"></script>
</body>
</html>
