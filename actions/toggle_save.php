<?php
session_start();
require_once __DIR__ . '/../app/models/SavedRecipe.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

try {
    $savedRecipeModel = new SavedRecipe();
    $user_id = $_SESSION['user_id'];
    $recipe_id = $_GET['id'];

    // [UPDATED]: Menggunakan stored procedure toggle_saved_recipe
    $action = $savedRecipeModel->toggleSave($user_id, $recipe_id);

    echo json_encode(['success' => true, 'action' => $action]);
} catch (Exception $e) {
    error_log("Error in toggle_save.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
