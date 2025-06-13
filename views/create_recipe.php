<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../app/models/Resep.php';

$error = '';
$success = '';

if ($_POST) {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $bahan = trim($_POST['bahan']);
    $langkah = trim($_POST['langkah']);
    $cuisine_type = $_POST['cuisine_type'] ?? 'Indonesian';
    $difficulty_level = $_POST['difficulty_level'] ?? 'Easy';
    $cooking_time = $_POST['cooking_time'] ?? 30;
    $servings = $_POST['servings'] ?? 4;
    $image_url = null;

    // Handle file upload
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['recipe_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
            $uploadDir = __DIR__ . '/../public/uploads/recipes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('recipe_') . '.' . $extension;
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $image_url = 'public/uploads/recipes/' . $filename;
            }
        }
    }

    if (empty($judul) || empty($bahan) || empty($langkah)) {
        $error = "Title, ingredients, and instructions are required!";
    } else {
        $resepModel = new Resep();
        if ($resepModel->addResep($_SESSION['user_id'], $judul, $deskripsi, $bahan, $langkah, $cuisine_type, $difficulty_level, $cooking_time, $servings, $image_url)) {
            $success = "Recipe added successfully!";
            // Clear form data
            $_POST = [];
        } else {
            $error = "Error adding recipe. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Recipe - CeritaRasa</title>
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
                <a href="create_recipe.php" class="nav-link active">
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

    <div class="add-recipe-container">
        <div class="add-recipe-header">
            <h1><i class="fas fa-plus-circle"></i> Add New Recipe</h1>
            <p>Share your culinary creation with the CeritaRasa community</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <p><?= htmlspecialchars($success) ?></p>
                <p><a href="my_recipes.php">View My Recipes</a> | <a href="create_recipe.php">Add Another Recipe</a></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="add-recipe-form" enctype="multipart/form-data">
            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-number">1</div>
                    <h2>Basic Information</h2>
                </div>
                
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="judul">Recipe Title *</label>
                        <input type="text" id="judul" name="judul" 
                               value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>" 
                               placeholder="Enter recipe title" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="deskripsi">Short Description</label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Brief description of your recipe" rows="3"><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="cuisine_type">Cuisine Type *</label>
                        <select id="cuisine_type" name="cuisine_type" required>
                            <option value="">Select cuisine type</option>
                            <option value="Indonesian" <?= (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] == 'Indonesian') ? 'selected' : '' ?>>Indonesian</option>
                            <option value="Italian" <?= (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] == 'Italian') ? 'selected' : '' ?>>Italian</option>
                            <option value="Asian" <?= (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] == 'Asian') ? 'selected' : '' ?>>Asian</option>
                            <option value="Mediterranean" <?= (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] == 'Mediterranean') ? 'selected' : '' ?>>Mediterranean</option>
                            <option value="American" <?= (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] == 'American') ? 'selected' : '' ?>>American</option>
                            <option value="Mexican" <?= (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] == 'Mexican') ? 'selected' : '' ?>>Mexican</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="difficulty_level">Difficulty Level *</label>
                        <select id="difficulty_level" name="difficulty_level" required>
                            <option value="">Select difficulty</option>
                            <option value="Easy" <?= (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] == 'Easy') ? 'selected' : '' ?>>Easy</option>
                            <option value="Medium" <?= (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="Hard" <?= (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] == 'Hard') ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="cooking_time">Cooking Time (minutes) *</label>
                        <input type="number" id="cooking_time" name="cooking_time" 
                               value="<?= isset($_POST['cooking_time']) ? $_POST['cooking_time'] : '30' ?>" 
                               placeholder="30" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="servings">Servings *</label>
                        <input type="number" id="servings" name="servings" 
                               value="<?= isset($_POST['servings']) ? $_POST['servings'] : '4' ?>" 
                               placeholder="4" min="1" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="recipe_image">Recipe Image</label>
                        <div class="image-upload-area" onclick="document.getElementById('recipe_image').click()">
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to choose image from your computer</p>
                                <p class="upload-info">Supported formats: JPG, PNG, GIF (Max 5MB)</p>
                            </div>
                            <div class="image-preview" id="imagePreview" style="display: none;">
                                <img id="previewImg" src="/placeholder.svg" alt="Preview">
                                <button type="button" class="remove-image" onclick="removeImage(event)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <input type="file" id="recipe_image" name="recipe_image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                    </div>
                </div>
            </div>

            <!-- Ingredients Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <h2>Ingredients *</h2>
                </div>
                
                <div class="form-group full-width">
                    <label for="bahan">List all ingredients (one per line)</label>
                    <textarea id="bahan" name="bahan" rows="8" placeholder="Example:&#10;2 cups rice&#10;1 chicken breast&#10;1 onion, diced&#10;2 cloves garlic" required><?= isset($_POST['bahan']) ? htmlspecialchars($_POST['bahan']) : '' ?></textarea>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <h2>Instructions *</h2>
                </div>
                
                <div class="form-group full-width">
                    <label for="langkah">Cooking steps (one step per line)</label>
                    <textarea id="langkah" name="langkah" rows="10" placeholder="Example:&#10;Heat oil in a large pan&#10;Add onion and garlic, cook until fragrant&#10;Add chicken and cook until golden&#10;Add rice and stir well" required><?= isset($_POST['langkah']) ? htmlspecialchars($_POST['langkah']) : '' ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="history.back()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Recipe
                </button>
            </div>
        </form>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 CeritaRasa. All rights reserved.</p>
        </div>
    </footer>

    <script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            input.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.match('image.*')) {
            alert('Please select a valid image file');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function removeImage(event) {
    event.stopPropagation();
    document.getElementById('recipe_image').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('imagePreview').style.display = 'none';
}
</script>
</body>
</html>
