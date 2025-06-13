<?php 
$title = "Dashboard";
include '../views/layout/header.php'; 
?>

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
                    <img src="/placeholder.svg?height=60&width=60" alt="User Avatar">
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
            <?php if (isset($latest_reseps) && !empty($latest_reseps)): ?>
                <?php foreach ($latest_reseps as $resep): ?>
                <div class="recipe-card">
                    <div class="recipe-image">
                        <img src="<?= $resep['image_url'] ?: '/placeholder.svg?height=200&width=300' ?>" alt="<?= htmlspecialchars($resep['judul']) ?>">
                        <div class="recipe-difficulty <?= strtolower($resep['difficulty_level']) ?>">
                            <?= htmlspecialchars($resep['difficulty_level']) ?>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <h3><?= htmlspecialchars($resep['judul']) ?></h3>
                        <p><?= htmlspecialchars(substr($resep['deskripsi'], 0, 100)) ?>...</p>
                        <div class="recipe-meta">
                            <span><i class="fas fa-clock"></i> <?= $resep['cooking_time'] ?> min</span>
                            <span><i class="fas fa-signal"></i> <?= $resep['difficulty_level'] ?></span>
                        </div>
                        <a href="/resep/show/<?= $resep['id'] ?>" class="btn-view-recipe">View Recipe</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada resep tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>
