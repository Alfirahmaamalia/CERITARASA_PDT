<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../app/models/Resep.php';

$resepModel = new Resep();
$reseps = $resepModel->getByUserId($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recipes - CeritaRasa</title>
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
                <a href="saved.php" class="nav-link">
                    <i class="fas fa-bookmark"></i> Saved Recipes
                </a>
                <a href="create_recipe.php" class="nav-link">
                    <i class="fas fa-plus"></i> Submit Recipe
                </a>
                <div class="nav-profile">
                    <i class="fas fa-user"></i> Profile
                    <div class="dropdown">
                        <a href="my_recipes.php" class="active">My Recipes</a>
                        <a href="../logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="my-recipes-container">
        <div class="page-header">
            <div>
                <h1>My Recipes</h1>
                <p>Manage your culinary creations</p>
            </div>
            <a href="create_recipe.php" class="btn-primary">
                <i class="fas fa-plus"></i> Add New Recipe
            </a>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <div class="search-container">
                <input type="text" placeholder="Search my recipes..." class="search-input" id="searchInput">
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

        <div class="recipes-stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($reseps) ?></div>
                <div class="stat-label">Total Recipes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= array_sum(array_column($reseps, 'total_tried')) ?></div>
                <div class="stat-label">Total Tried</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($reseps, function($r) { return $r['difficulty_level'] === 'Easy'; })) ?></div>
                <div class="stat-label">Easy Recipes</div>
            </div>
        </div>

        <div class="recipes-grid">
            <?php if (!empty($reseps)): ?>
                <?php foreach ($reseps as $resep): ?>
                <div class="recipe-card my-recipe-card" data-difficulty="<?= $resep['difficulty_level'] ?>">
                    <div class="recipe-image">
                        <img src="<?= $resep['image_url'] ? '../' . $resep['image_url'] : '/placeholder.svg?height=200&width=300' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>">
                        <div class="recipe-difficulty <?= strtolower($resep['difficulty_level']) ?>">
                            <?= htmlspecialchars($resep['difficulty_level']) ?>
                        </div>
                        <div class="recipe-actions-overlay">
                            <button class="action-btn edit-btn" onclick="editRecipe(<?= $resep['id'] ?>)" title="Edit Recipe">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="deleteRecipe(<?= $resep['id'] ?>)" title="Delete Recipe">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <h3><?= htmlspecialchars($resep['judul']) ?></h3>
                        <p><?= htmlspecialchars(substr($resep['deskripsi'], 0, 100)) ?>...</p>
                        <div class="recipe-meta">
                            <span><i class="fas fa-clock"></i> <?= $resep['cooking_time'] ?> min</span>
                            <span><i class="fas fa-utensils"></i> <?= $resep['servings'] ?> servings</span>
                            <span><i class="fas fa-heart"></i> <?= $resep['total_tried'] ?> tried</span>
                        </div>
                        <div class="recipe-actions">
                            <a href="recipe_detail.php?id=<?= $resep['id'] ?>" class="btn-view-recipe">View Recipe</a>
                            <button class="btn-secondary" onclick="shareRecipe(<?= $resep['id'] ?>)">
                                <i class="fas fa-share"></i> Share
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-chef-hat fa-3x"></i>
                    <h3>No Recipes Yet</h3>
                    <p>Start sharing your delicious recipes with the community!</p>
                    <a href="create_recipe.php" class="btn-primary">Create Your First Recipe</a>
                </div>
            <?php endif; ?>
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
            const recipes = document.querySelectorAll('.recipe-card');
            
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
        const recipes = document.querySelectorAll('.recipe-card');
        
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

    function editRecipe(recipeId) {
        // For now, redirect to create page with edit parameter
        alert('Edit functionality will be implemented soon!');
    }

    function deleteRecipe(recipeId) {
        if (confirm('Are you sure you want to delete this recipe? This action cannot be undone.')) {
            fetch(`../actions/delete_recipe.php?id=${recipeId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting recipe. Please try again.');
                }
            });
        }
    }

    function shareRecipe(recipeId) {
        const url = `${window.location.origin}/views/recipe_detail.php?id=${recipeId}`;
        
        if (navigator.share) {
            navigator.share({
                title: 'Check out this recipe!',
                url: url
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(url).then(() => {
                alert('Recipe link copied to clipboard!');
            });
        }
    }
    </script>
</body>
</html>
