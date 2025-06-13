<?php 
$title = "All Recipes";
include '../views/layout/header.php'; 
?>

<div class="recipes-container">
    <div class="page-header">
        <h1>All Recipes</h1>
        <p>Discover amazing recipes from our community</p>
    </div>
    
    <div class="search-section">
        <div class="search-container">
            <input type="text" placeholder="Search for recipes, ingredients, or cuisine..." class="search-input" id="searchInput">
            <button class="search-btn" onclick="searchRecipes()">Search</button>
        </div>
    </div>

    <div class="filters-section">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="Easy">Easy</button>
            <button class="filter-btn" data-filter="Medium">Medium</button>
            <button class="filter-btn" data-filter="Hard">Hard</button>
        </div>
    </div>

    <div class="recipes-grid">
        <?php if (!empty($reseps)): ?>
            <?php foreach ($reseps as $resep): ?>
            <div class="recipe-card" data-difficulty="<?= $resep['difficulty_level'] ?>">
                <div class="recipe-image">
                    <img src="<?= $resep['image_url'] ?: '/placeholder.svg?height=200&width=300' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>">
                    <div class="recipe-difficulty <?= strtolower($resep['difficulty_level']) ?>">
                        <?= htmlspecialchars($resep['difficulty_level']) ?>
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="save-recipe-btn" onclick="toggleSave(<?= $resep['id'] ?>)">
                        <i class="far fa-heart"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <div class="recipe-content">
                    <h3><?= htmlspecialchars($resep['judul']) ?></h3>
                    <p class="recipe-author">By <?= htmlspecialchars($resep['username']) ?></p>
                    <p><?= htmlspecialchars(substr($resep['deskripsi'], 0, 100)) ?>...</p>
                    <div class="recipe-meta">
                        <span><i class="fas fa-clock"></i> <?= $resep['cooking_time'] ?> min</span>
                        <span><i class="fas fa-utensils"></i> <?= $resep['servings'] ?> servings</span>
                        <span><i class="fas fa-heart"></i> <?= $resep['total_tried'] ?> tried</span>
                    </div>
                    <a href="/resep/show/<?= $resep['id'] ?>" class="btn-view-recipe">View Recipe</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-utensils fa-3x"></i>
                <h3>No Recipes Found</h3>
                <p>Be the first to share a recipe!</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/resep/create" class="btn-primary">Add Your Recipe</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

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

// Save recipe functionality
function toggleSave(recipeId) {
    fetch(`/saved/toggle/${recipeId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Toggle heart icon
            const btn = document.querySelector(`button[onclick="toggleSave(${recipeId})"]`);
            const icon = btn.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
        }
    });
}
</script>

<?php include '../views/layout/footer.php'; ?>
