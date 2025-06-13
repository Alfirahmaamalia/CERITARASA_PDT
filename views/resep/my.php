<?php 
$title = "My Recipes";
include '../views/layout/header.php'; 
?>

<div class="my-recipes-container">
    <div class="page-header">
        <h1>My Recipes</h1>
        <p>Manage your culinary creations</p>
        <a href="/resep/create" class="btn-primary">
            <i class="fas fa-plus"></i> Add New Recipe
        </a>
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
            <div class="recipe-card my-recipe-card">
                <div class="recipe-image">
                    <img src="<?= $resep['image_url'] ?: '/placeholder.svg?height=200&width=300' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>">
                    <div class="recipe-difficulty <?= strtolower($resep['difficulty_level']) ?>">
                        <?= htmlspecialchars($resep['difficulty_level']) ?>
                    </div>
                    <div class="recipe-actions-overlay">
                        <button class="action-btn edit-btn" onclick="editRecipe(<?= $resep['id'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteRecipe(<?= $resep['id'] ?>)">
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
                        <a href="/resep/show/<?= $resep['id'] ?>" class="btn-view-recipe">View Recipe</a>
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
                <a href="/resep/create" class="btn-primary">Create Your First Recipe</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function editRecipe(recipeId) {
    // For now, redirect to create page with edit parameter
    window.location.href = `/resep/edit/${recipeId}`;
}

function deleteRecipe(recipeId) {
    if (confirm('Are you sure you want to delete this recipe? This action cannot be undone.')) {
        fetch(`/resep/delete/${recipeId}`, {
            method: 'DELETE',
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
    const url = `${window.location.origin}/resep/show/${recipeId}`;
    
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

<style>
.my-recipes-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.recipes-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #ff6b6b;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 1.1rem;
}

.my-recipe-card {
    position: relative;
}

.recipe-actions-overlay {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.2s;
}

.my-recipe-card:hover .recipe-actions-overlay {
    opacity: 1;
}

.action-btn {
    background: rgba(255,255,255,0.9);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.edit-btn:hover {
    background: #007bff;
    color: white;
}

.delete-btn:hover {
    background: #dc3545;
    color: white;
}

.recipe-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn-secondary {
    background: white;
    color: #ff6b6b;
    border: 1px solid #ff6b6b;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: #ff6b6b;
    color: white;
}
</style>

<?php include '../views/layout/footer.php'; ?>
