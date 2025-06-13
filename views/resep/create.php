<?php 
$title = "Add New Recipe";
include '../views/layout/header.php'; 
?>

<div class="add-recipe-container">
    <div class="add-recipe-header">
        <h1><i class="fas fa-plus-circle"></i> Add New Recipe</h1>
        <p>Share your culinary creation with the CeritaRasa community</p>
    </div>

    <form method="POST" action="/resep/create" class="add-recipe-form" enctype="multipart/form-data">
        <!-- Basic Information Section -->
        <div class="form-section">
            <div class="section-header">
                <div class="section-number">1</div>
                <h2>Basic Information</h2>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="judul">Recipe Title *</label>
                    <input type="text" id="judul" name="judul" placeholder="Enter recipe title" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="deskripsi">Short Description *</label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Brief description of your recipe" rows="3" required></textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cuisine_type">Cuisine Type *</label>
                    <select id="cuisine_type" name="cuisine_type" required>
                        <option value="">Select cuisine type</option>
                        <option value="Indonesian">Indonesian</option>
                        <option value="Italian">Italian</option>
                        <option value="Asian">Asian</option>
                        <option value="Mediterranean">Mediterranean</option>
                        <option value="American">American</option>
                        <option value="Mexican">Mexican</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="difficulty_level">Difficulty Level *</label>
                    <select id="difficulty_level" name="difficulty_level" required>
                        <option value="">Select difficulty</option>
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cooking_time">Cooking Time (minutes) *</label>
                    <input type="number" id="cooking_time" name="cooking_time" placeholder="30" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="servings">Servings *</label>
                    <input type="number" id="servings" name="servings" placeholder="4" min="1" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="image_url">Upload Image</label>
                    <div class="image-upload-area">
                        <div class="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drop image here or click to browse</p>
                            <button type="button" class="choose-file-btn">Choose File</button>
                        </div>
                        <input type="url" id="image_url" name="image_url" placeholder="Or enter image URL" style="margin-top: 1rem;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingredients Section -->
        <div class="form-section">
            <div class="section-header">
                <div class="section-number">2</div>
                <h2>Ingredients *</h2>
            </div>
            
            <div class="ingredients-container">
                <div class="ingredient-item">
                    <span class="ingredient-number">1.</span>
                    <input type="text" name="ingredients[]" placeholder="Enter ingredient" class="ingredient-input">
                    <button type="button" class="remove-ingredient" onclick="removeIngredient(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <button type="button" class="add-ingredient-btn" onclick="addIngredient()">
                <i class="fas fa-plus"></i> Add Ingredient
            </button>
        </div>

        <!-- Instructions Section -->
        <div class="form-section">
            <div class="section-header">
                <div class="section-number">3</div>
                <h2>Instructions *</h2>
            </div>
            
            <div class="instructions-container">
                <div class="instruction-item">
                    <div class="instruction-number">1</div>
                    <textarea name="instructions[]" placeholder="Describe this step in detail" class="instruction-input" rows="3"></textarea>
                    <button type="button" class="remove-instruction" onclick="removeInstruction(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <button type="button" class="add-step-btn" onclick="addInstruction()">
                <i class="fas fa-plus"></i> Add Step
            </button>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="history.back()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn-preview">
                <i class="fas fa-eye"></i> Preview
            </button>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save Recipe
            </button>
        </div>
    </form>
</div>

<script>
let ingredientCount = 1;
let instructionCount = 1;

function addIngredient() {
    ingredientCount++;
    const container = document.querySelector('.ingredients-container');
    const newIngredient = document.createElement('div');
    newIngredient.className = 'ingredient-item';
    newIngredient.innerHTML = `
        <span class="ingredient-number">${ingredientCount}.</span>
        <input type="text" name="ingredients[]" placeholder="Enter ingredient" class="ingredient-input">
        <button type="button" class="remove-ingredient" onclick="removeIngredient(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newIngredient);
}

function removeIngredient(button) {
    if (document.querySelectorAll('.ingredient-item').length > 1) {
        button.parentElement.remove();
        updateIngredientNumbers();
    }
}

function updateIngredientNumbers() {
    const items = document.querySelectorAll('.ingredient-item');
    items.forEach((item, index) => {
        item.querySelector('.ingredient-number').textContent = (index + 1) + '.';
    });
    ingredientCount = items.length;
}

function addInstruction() {
    instructionCount++;
    const container = document.querySelector('.instructions-container');
    const newInstruction = document.createElement('div');
    newInstruction.className = 'instruction-item';
    newInstruction.innerHTML = `
        <div class="instruction-number">${instructionCount}</div>
        <textarea name="instructions[]" placeholder="Describe this step in detail" class="instruction-input" rows="3"></textarea>
        <button type="button" class="remove-instruction" onclick="removeInstruction(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newInstruction);
}

function removeInstruction(button) {
    if (document.querySelectorAll('.instruction-item').length > 1) {
        button.parentElement.remove();
        updateInstructionNumbers();
    }
}

function updateInstructionNumbers() {
    const items = document.querySelectorAll('.instruction-item');
    items.forEach((item, index) => {
        item.querySelector('.instruction-number').textContent = index + 1;
    });
    instructionCount = items.length;
}

// Form submission handler
document.querySelector('.add-recipe-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Collect ingredients
    const ingredients = Array.from(document.querySelectorAll('input[name="ingredients[]"]'))
        .map(input => input.value.trim())
        .filter(value => value !== '')
        .join('\n');
    
    // Collect instructions
    const instructions = Array.from(document.querySelectorAll('textarea[name="instructions[]"]'))
        .map(textarea => textarea.value.trim())
        .filter(value => value !== '')
        .join('\n');
    
    // Create hidden inputs for the actual form data
    const form = this;
    
    // Remove existing hidden inputs
    form.querySelectorAll('input[name="bahan"], input[name="langkah"]').forEach(input => input.remove());
    
    // Add new hidden inputs
    const bahanInput = document.createElement('input');
    bahanInput.type = 'hidden';
    bahanInput.name = 'bahan';
    bahanInput.value = ingredients;
    form.appendChild(bahanInput);
    
    const langkahInput = document.createElement('input');
    langkahInput.type = 'hidden';
    langkahInput.name = 'langkah';
    langkahInput.value = instructions;
    form.appendChild(langkahInput);
    
    // Submit the form
    form.submit();
});
</script>

<?php include '../views/layout/footer.php'; ?>
