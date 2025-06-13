<?php
session_start();
require_once __DIR__ . '/../app/models/Resep.php';
require_once __DIR__ . '/../app/models/SavedRecipe.php';

if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$resepModel = new Resep();
$resep = $resepModel->getByIdWithUser($_GET['id']);

if (!$resep) {
    header('Location: ../index.php');
    exit();
}

$is_tried = false;
$is_saved = false;

if (isset($_SESSION['user_id'])) {
    $is_tried = $resepModel->isTriedByUser($_GET['id'], $_SESSION['user_id']);
    
    $savedRecipeModel = new SavedRecipe();
    $is_saved = $savedRecipeModel->isSaved($_SESSION['user_id'], $_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($resep['judul']) ?> - CeritaRasa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../public/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="../index.php">CeritaRasa</a>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="saved.php" class="nav-link">
                    <i class="fas fa-bookmark"></i> Saved Recipes
                </a>
                <a href="create_recipe.php" class="nav-link">
                    <i class="fas fa-plus"></i> Submit Recipe
                </a>
                <div class="nav-profile">
                    <i class="fas fa-user"></i> Profile
                    <div class="dropdown">
                        <a href="my_recipes.php">My Recipes</a>
                        <a href="../logout.php">Logout</a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="nav-menu">
                <a href="../login.php" class="nav-link">Masuk</a>
                <a href="../register.php" class="btn-register">Daftar</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="recipe-detail-container">
        <div class="recipe-detail-content">
            <!-- Recipe Header -->
            <div class="recipe-header">
                <div class="recipe-image-container">
                    <img src="<?= $resep['image_url'] ? '../' . $resep['image_url'] : '/placeholder.svg?height=400&width=600' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>" class="recipe-main-image">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="save-recipe-btn <?= $is_saved ? 'saved' : '' ?>" onclick="toggleSave(<?= $resep['id'] ?>)">
                        <i class="<?= $is_saved ? 'fas' : 'far' ?> fa-heart"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recipe Info -->
            <div class="recipe-info-section">
                <h1 class="recipe-title"><?= htmlspecialchars($resep['judul']) ?></h1>
                <p class="recipe-author">By <?= htmlspecialchars($resep['username']) ?></p>
                <p class="recipe-description"><?= htmlspecialchars($resep['deskripsi']) ?></p>
                
                <div class="recipe-stats">
                    <div class="stat-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="stat-label">Cooking Time</span>
                            <span class="stat-value"><?= $resep['cooking_time'] ?> minutes</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-signal"></i>
                        <div>
                            <span class="stat-label">Difficulty</span>
                            <span class="stat-value"><?= $resep['difficulty_level'] ?></span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-utensils"></i>
                        <div>
                            <span class="stat-label">Serves</span>
                            <span class="stat-value"><?= $resep['servings'] ?> servings</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingredients Section -->
            <div class="recipe-section">
                <h2 class="section-title">
                    <i class="fas fa-list"></i> Ingredients
                </h2>
                <div class="ingredients-list">
                    <?php 
                    $ingredients = explode("\n", $resep['bahan']);
                    foreach ($ingredients as $ingredient): 
                        if (trim($ingredient)):
                    ?>
                    <div class="ingredient-item">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars(trim($ingredient)) ?></span>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="recipe-section">
                <h2 class="section-title">
                    <i class="fas fa-list-ol"></i> Cooking Instructions
                </h2>
                <div class="instructions-list">
                    <?php 
                    $instructions = explode("\n", $resep['langkah']);
                    $step = 1;
                    foreach ($instructions as $instruction): 
                        if (trim($instruction)):
                    ?>
                    <div class="instruction-step">
                        <div class="step-number"><?= $step ?></div>
                        <div class="step-content">
                            <p><?= htmlspecialchars(trim($instruction)) ?></p>
                        </div>
                    </div>
                    <?php 
                        $step++;
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="recipe-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn-primary" onclick="toggleTried(<?= $resep['id'] ?>)">
                    <i class="fas fa-check"></i>
                    <?= $is_tried ? 'Mark as Not Tried' : 'Mark as Tried' ?>
                </button>
                <button class="btn-secondary" onclick="toggleSave(<?= $resep['id'] ?>)">
                    <i class="<?= $is_saved ? 'fas' : 'far' ?> fa-heart"></i>
                    <?= $is_saved ? 'Unsave Recipe' : 'Save Recipe' ?>
                </button>
                <?php else: ?>
                <a href="../login.php" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to Save Recipe
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 CeritaRasa. All rights reserved.</p>
        </div>
    </footer>

    <script>
    function toggleSave(recipeId) {
        fetch(`../actions/toggle_save.php?id=${recipeId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function toggleTried(recipeId) {
        fetch(`../actions/toggle_tried.php?id=${recipeId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            location.reload();
        });
    }
    </script>
</body>
</html>
