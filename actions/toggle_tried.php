<?php
session_start();
require_once __DIR__ . '/../app/models/ResepDicoba.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

try {
    $resepDicobaModel = new ResepDicoba();
    $user_id = $_SESSION['user_id'];
    $recipe_id = $_GET['id'];

    // [UPDATED]: Menggunakan stored procedure toggle_resep_dicoba
    $action = $resepDicobaModel->toggleTried($user_id, $recipe_id);

    // Return JSON response for AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'action' => $action]);
        exit;
    }

    header('Location: ../views/recipe_detail.php?id=' . $recipe_id);
} catch (Exception $e) {
    error_log("Error in toggle_tried.php: " . $e->getMessage());
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }

    header('Location: ../views/recipe_detail.php?id=' . $recipe_id);
}
?>
