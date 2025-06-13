<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        require_once '../views/' . $view . '.php';
    }

    public function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/auth/login');
        }
    }
}
?>
