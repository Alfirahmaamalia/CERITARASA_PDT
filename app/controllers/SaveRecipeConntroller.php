<?php
require_once '../app/core/Controller.php';
require_once '../app/models/SavedRecipe.php';

class SavedRecipeController extends Controller {
    private $savedRecipeModel;

    public function __construct() {
        $this->savedRecipeModel = new SavedRecipe();
    }

    public function index() {
        $this->requireLogin();
        $saved_recipes = $this->savedRecipeModel->getSavedRecipes($_SESSION['user_id']);
        $this->view('saved/index', ['saved_recipes' => $saved_recipes]);
    }

    // [UPDATED]: Menggunakan stored procedure toggle_saved_recipe dengan transaction
    public function toggle($resep_id) {
        $this->requireLogin();
        
        try {
            $action = $this->savedRecipeModel->toggleSave($_SESSION['user_id'], $resep_id);

            // Return JSON response for AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'action' => $action]);
                exit;
            }

            $this->redirect('/resep/show/' . $resep_id);
        } catch (Exception $e) {
            error_log("Error in toggle save: " . $e->getMessage());
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }

            $this->redirect('/resep/show/' . $resep_id);
        }
    }
}
?>
