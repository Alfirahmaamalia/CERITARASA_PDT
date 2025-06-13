<?php 
$title = htmlspecialchars($resep['judul']);
include '../views/layout/header.php'; 
?>

<div class="recipe-detail-container">
    <div class="recipe-detail-content">
        <!-- Recipe Header -->
        <div class="recipe-header">
            <div class="recipe-image-container">
                <img src="<?= $resep['image_url'] ?: '/placeholder.svg?height=400&width=600' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>" class="recipe-main-image">
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
                        <span class="stat-value"><?= $resep['servings'] ?> bowls</span>
                    </div>
                </div>
                <!-- [UPDATED]: Menggunakan stored function get_total_tried untuk menampilkan jumlah tried -->
                <div class="stat-item">
                    <i class="fas fa-heart"></i>
                    <div>
                        <span class="stat-label">Tried by</span>
                        <span class="stat-value"><?= $resep['total_tried'] ?> people</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingredients Section -->
        <div class="recipe-section">
            <h2 class="section-title">
                <i class="fas fa-list"></i> Ingredients
            </h2>
            <div class="ingredients-grid">
                <?php 
                $ingredients = explode("\n", $resep['bahan']);
                $half = ceil(count($ingredients) / 2);
                $left_ingredients = array_slice($ingredients, 0, $half);
                $right_ingredients = array_slice($ingredients, $half);
                ?>
                
                <div class="ingredients-column">
                    <h3>Base & Protein</h3>
                    <ul class="ingredients-list">
                        <?php foreach ($left_ingredients as $ingredient): ?>
                            <?php if (trim($ingredient)): ?>
                            <li><?= htmlspecialchars(trim($ingredient)) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="ingredients-column">
                    <h3>Fresh Vegetables</h3>
                    <ul class="ingredients-list">
                        <?php foreach ($right_ingredients as $ingredient): ?>
                            <?php if (trim($ingredient)): ?>
                            <li><?= htmlspecialchars(trim($ingredient)) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
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
                        <h4><?= $step == 1 ? 'Prepare the quinoa' : ($step == 2 ? 'Cook the tofu' : ($step == 3 ? 'Prepare vegetables' : 'Assemble the bowls')) ?></h4>
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
            <a href="/auth/login" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Login to Save Recipe
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// [UPDATED]: Menggunakan stored procedure untuk toggle save dan tried
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
        } else {
            console.error('Error:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function toggleTried(recipeId) {
    fetch(`/resep/toggle-tried/${recipeId}`, {
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
            console.error('Error:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback to page reload
        location.reload();
    });
}
</script>

<?php include '../views/layout/footer.php'; ?>
