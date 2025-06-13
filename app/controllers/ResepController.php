<?php
require_once '../app/core/Controller.php';
require_once '../app/models/Resep.php';
require_once '../app/models/ResepDicoba.php';

class ResepController extends Controller {
    private $resepModel;
    private $resepDicobaModel;

    public function __construct() {
        $this->resepModel = new Resep();
        $this->resepDicobaModel = new ResepDicoba();
    }

    public function index() {
        $reseps = $this->resepModel->getAllWithUser();
        $this->view('resep/index', ['reseps' => $reseps]);
    }

    public function show($id) {
        $resep = $this->resepModel->getByIdWithUser($id);
        
        if (!$resep) {
            $this->redirect('/resep');
        }

        $is_tried = false;
        $is_saved = false;
        
        if ($this->isLoggedIn()) {
            $is_tried = $this->resepModel->isTriedByUser($id, $_SESSION['user_id']);
            
            require_once '../app/models/SavedRecipe.php';
            $savedRecipeModel = new SavedRecipe();
            $is_saved = $savedRecipeModel->isSaved($_SESSION['user_id'], $id);
        }

        $this->view('resep/show', [
            'resep' => $resep,
            'is_tried' => $is_tried,
            'is_saved' => $is_saved
        ]);
    }

    // [UPDATED]: Menggunakan stored procedure add_resep dengan transaction untuk upload gambar
    public function create() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = $_POST['judul'];
            $deskripsi = $_POST['deskripsi'];
            $bahan = $_POST['bahan'];
            $langkah = $_POST['langkah'];
            $cuisine_type = $_POST['cuisine_type'] ?? 'Indonesian';
            $difficulty_level = $_POST['difficulty_level'] ?? 'Easy';
            $cooking_time = $_POST['cooking_time'] ?? 30;
            $servings = $_POST['servings'] ?? 4;
            $image_url = null;

            if (empty($judul) || empty($bahan) || empty($langkah)) {
                $error = "Judul, bahan, dan langkah harus diisi!";
                $this->view('resep/create', ['error' => $error]);
                return;
            }

            try {
                // [UPDATED]: Menggunakan transaction untuk proses yang melibatkan lebih dari satu aksi
                $database = new Database();
                $db = $database->getConnection();
                $db->beginTransaction();

                // Handle file upload if exists
                if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['recipe_image'];
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $maxSize = 5 * 1024 * 1024; // 5MB

                    if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                        $uploadDir = __DIR__ . '/../../public/uploads/recipes/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $filename = uniqid('recipe_') . '.' . $extension;
                        $filepath = $uploadDir . $filename;

                        if (move_uploaded_file($file['tmp_name'], $filepath)) {
                            $image_url = 'public/uploads/recipes/' . $filename;
                        } else {
                            throw new Exception("Failed to upload image");
                        }
                    } else {
                        throw new Exception("Invalid file type or size");
                    }
                }

                // [UPDATED]: Menggunakan stored procedure add_resep
                $resep_id = $this->resepModel->addResep(
                    $_SESSION['user_id'], 
                    $judul, 
                    $deskripsi, 
                    $bahan, 
                    $langkah, 
                    $cuisine_type, 
                    $difficulty_level, 
                    $cooking_time, 
                    $servings, 
                    $image_url
                );

                if ($resep_id) {
                    $db->commit();
                    $this->redirect('/resep/my');
                } else {
                    throw new Exception("Failed to add recipe");
                }

            } catch (Exception $e) {
                if (isset($db)) {
                    $db->rollBack();
                }
                
                // Clean up uploaded file if exists
                if (isset($filepath) && file_exists($filepath)) {
                    unlink($filepath);
                }
                
                error_log("Error in create recipe: " . $e->getMessage());
                $error = "Terjadi kesalahan saat menambah resep!";
                $this->view('resep/create', ['error' => $error]);
            }
        } else {
            $this->view('resep/create');
        }
    }

    public function my() {
        $this->requireLogin();
        $reseps = $this->resepModel->getByUserId($_SESSION['user_id']);
        $this->view('resep/my', ['reseps' => $reseps]);
    }

    // [UPDATED]: Menggunakan stored procedure untuk toggle tried
    public function toggleTried($id) {
        $this->requireLogin();

        try {
            $action = $this->resepDicobaModel->toggleTried($_SESSION['user_id'], $id);
            
            // Return JSON response for AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'action' => $action]);
                exit;
            }

            $this->redirect('/resep/show/' . $id);
        } catch (Exception $e) {
            error_log("Error in toggleTried: " . $e->getMessage());
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }

            $this->redirect('/resep/show/' . $id);
        }
    }
}
?>
