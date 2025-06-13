<?php
require_once '../app/core/Controller.php';
require_once '../app/models/Resep.php';

class HomeController extends Controller {
    private $resepModel;

    public function __construct() {
        $this->resepModel = new Resep();
    }

    public function index() {
        $latest_reseps = $this->resepModel->getAllWithUser();
        // Ambil 6 resep terbaru
        $latest_reseps = array_slice($latest_reseps, 0, 6);
        
        $this->view('home', ['latest_reseps' => $latest_reseps]);
    }
}
?>
