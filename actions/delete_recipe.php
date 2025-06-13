<?php
session_start();
require_once __DIR__ . '/../app/models/Resep.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$resepModel = new Resep();
$recipe_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if the recipe belongs to the current user
$recipe = $resepModel->getByIdWithUser($recipe_id);
if (!$recipe || $recipe['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$result = $resepModel->deleteRecipe($recipe_id);
echo json_encode(['success' => $result]);
?>
