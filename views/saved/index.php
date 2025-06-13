<?php 
$title = "Saved Recipes";
include '../views/layout/header.php'; 
?>

<div class="saved-recipes-container">
    <div class="page-header">
        <h1>Recipe Dashboard</h1>
        <p>Discover and manage your favorite recipes</p>
    </div>

    <div class="recipes-dashboard">
        <?php if (!empty($saved_recipes)): ?>
            <?php foreach ($saved_recipes as $index => $resep): ?>
            <div class="recipe-dashboard-card">
                <div class="recipe-card-header">
                    <div class="difficulty-badge <?= strtolower($resep['difficulty_level']) ?>">
                        <?= $resep['difficulty_level'] ?>
                    </div>
                    <div class="cooking-time">
                        <i class="fas fa-clock"></i> <?= $resep['cooking_time'] ?> min
                    </div>
                </div>
                
                <div class="recipe-card-image">
                    <img src="<?= $resep['image_url'] ?: '/placeholder.svg?height=200&width=300' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>">
                </div>
                
                <div class="recipe-card-content">
                    <h3><?= htmlspecialchars($resep['judul']) ?></h3>
                    <p><?= htmlspecialchars(substr($resep['deskripsi'], 0, 100)) ?>...</p>
                    
                    <div class="recipe-details">
                        <div class="ingredients-section">
                            <h4>Ingredients:</h4>
                            <ul>
                                <?php 
                                $ingredients = array_slice(explode("\n", $resep['bahan']), 0, 6);
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
                        
                        <div class="instructions-section">
                            <h4>Instructions:</h4>
                            <ol>
                                <?php 
                                $instructions = array_slice(explode("\n", $resep['langkah']), 0, 4);
                                foreach ($instructions as $instruction): 
                                    if (trim($instruction)):
                                ?>
                                <li><?= htmlspecialchars(trim($instruction)) ?></li>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="recipe-tags">
                        <span class="tag cuisine-tag"><?= $resep['cuisine_type'] ?></span>
                        <span class="tag difficulty-tag"><?= $resep['difficulty_level'] ?></span>
                    </div>
                    
                    <div class="recipe-actions">
                        <button class="save-btn saved" onclick="toggleSave(<?= $resep['id'] ?>)">
                            <i class="fas fa-heart"></i> Save
                        </button>
                        <a href="/resep/show/<?= $resep['id'] ?>" class="view-btn">View Full Recipe</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bookmark fa-3x"></i>
                <h3>No Saved Recipes Yet</h3>
                <p>Start exploring and save your favorite recipes!</p>
                <a href="/" class="btn-primary">Browse Recipes</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-number">127</div>
            <div class="stat-label">Total Recipes</div>
        </div>
        <div class="stat-card favorites">
            <div class="stat-number"><?= count($saved_recipes) ?></div>
            <div class="stat-label">Favorites</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">15</div>
            <div class="stat-label">Categories</div>
        </div>
    </div>
</div>

<script>
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
            location.reload();
        }
    });
}
</script>

<?php include '../views/layout/footer.php'; ?>
