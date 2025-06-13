<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../app/models/SavedRecipe.php';

$savedRecipeModel = new SavedRecipe();
$saved_recipes = $savedRecipeModel->getSavedRecipes($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Recipes - CeritaRasa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../public/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="../index.php">CeritaRasa</a>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="saved.php" class="nav-link active">
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
        </div>
    </nav>

    <div class="saved-recipes-container">
        <div class="page-header">
            <h1>Saved Recipes</h1>
            <p>Your favorite recipes collection</p>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <div class="search-container">
                <input type="text" placeholder="Search saved recipes..." class="search-input" id="searchInput">
                <button class="search-btn" onclick="searchRecipes()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="Easy">Easy</button>
                <button class="filter-btn" data-filter="Medium">Medium</button>
                <button class="filter-btn" data-filter="Hard">Hard</button>
            </div>
        </div>

        <div class="recipes-dashboard">
            <?php if (!empty($saved_recipes)): ?>
                <?php foreach ($saved_recipes as $resep): ?>
                <div class="recipe-dashboard-card" data-difficulty="<?= $resep['difficulty_level'] ?>">
                    <div class="recipe-card-header">
                        <div class="difficulty-badge <?= strtolower($resep['difficulty_level']) ?>">
                            <?= $resep['difficulty_level'] ?>
                        </div>
                        <div class="cooking-time">
                            <i class="fas fa-clock"></i> <?= $resep['cooking_time'] ?> min
                        </div>
                    </div>
                    
                    <div class="recipe-card-image">
                        <img src="<?= $resep['image_url'] ? '../' . $resep['image_url'] : '/placeholder.svg?height=200&width=300' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>">
                    </div>
                    
                    <div class="recipe-card-content">
                        <h3><?= htmlspecialchars($resep['judul']) ?></h3>
                        <p class="recipe-author">By <?= htmlspecialchars($resep['username']) ?></p>
                        <p><?= htmlspecialchars(substr($resep['deskripsi'], 0, 100)) ?>...</p>
                        
                        <div class="recipe-details">
                            <div class="ingredients-section">
                                <h4>Ingredients:</h4>
                                <ul>
                                    <?php 
                                    $ingredients = array_slice(explode("\n", $resep['bahan']), 0, 4);
                                    foreach ($ingredients as $ingredient): 
                                        if (trim($ingredient)):
                                    ?>
                                    <li><?= htmlspecialchars(trim($ingredient)) ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="recipe-tags">
                            <span class="tag cuisine-tag"><?= $resep['cuisine_type'] ?></span>
                            <span class="tag difficulty-tag"><?= $resep['difficulty_level'] ?></span>
                        </div>
                        
                        <div class="recipe-actions">
                            <button class="save-btn saved" onclick="toggleSave(<?= $resep['id'] ?>)">
                                <i class="fas fa-heart"></i> Saved
                            </button>
                            <a href="recipe_detail.php?id=<?= $resep['id'] ?>" class="view-btn">View Recipe</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-bookmark fa-3x"></i>
                    <h3>No Saved Recipes Yet</h3>
                    <p>Start exploring and save your favorite recipes!</p>
                    <a href="dashboard.php" class="btn-primary">Browse Recipes</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="dashboard-stats">
            <div class="stat-card favorites">
                <div class="stat-number"><?= count($saved_recipes) ?></div>
                <div class="stat-label">Saved Recipes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($saved_recipes, function($r) { return $r['difficulty_level'] === 'Easy'; })) ?></div>
                <div class="stat-label">Easy Recipes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($saved_recipes, function($r) { return $r['cuisine_type'] === 'Indonesian'; })) ?></div>
                <div class="stat-label">Indonesian</div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 CeritaRasa. All rights reserved.</p>
        </div>
    </footer>

    <script>
    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            const recipes = document.querySelectorAll('.recipe-dashboard-card');
            
            recipes.forEach(recipe => {
                if (filter === 'all' || recipe.getAttribute('data-difficulty') === filter) {
                    recipe.style.display = 'block';
                } else {
                    recipe.style.display = 'none';
                }
            });
        });
    });

    // Search functionality
    function searchRecipes() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const recipes = document.querySelectorAll('.recipe-dashboard-card');
        
        recipes.forEach(recipe => {
            const title = recipe.querySelector('h3').textContent.toLowerCase();
            const description = recipe.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                recipe.style.display = 'block';
            } else {
                recipe.style.display = 'none';
            }
        });
    }

    // Real-time search
    document.getElementById('searchInput').addEventListener('input', searchRecipes);

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
    </script>
</body>
</html>
