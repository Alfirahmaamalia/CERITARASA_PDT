<?php
session_start();

// Autoload core files
require_once '../app/core/Database.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';

// Simple routing
$url = $_SERVER['REQUEST_URI'];
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);

// Remove query string
$url = strtok($url, '?');

// Default route
if (empty($url)) {
    $url = '/';
}

// Route handling
switch ($url) {
    case '/':
        require_once '../app/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
        
    case '/auth/login':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case '/auth/register':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;
        
    case '/auth/logout':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case '/resep':
        require_once '../app/controllers/ResepController.php';
        $controller = new ResepController();
        $controller->index();
        break;
        
    case '/resep/create':
        require_once '../app/controllers/ResepController.php';
        $controller = new ResepController();
        $controller->create();
        break;
        
    case '/resep/my':
        require_once '../app/controllers/ResepController.php';
        $controller = new ResepController();
        $controller->my();
        break;
        
    case '/saved':
        require_once '../app/controllers/SavedRecipeController.php';
        $controller = new SavedRecipeController();
        $controller->index();
        break;
        
    default:
        // Handle dynamic routes like /resep/show/1
        if (preg_match('/^\/resep\/show\/(\d+)$/', $url, $matches)) {
            require_once '../app/controllers/ResepController.php';
            $controller = new ResepController();
            $controller->show($matches[1]);
        } elseif (preg_match('/^\/resep\/toggle-tried\/(\d+)$/', $url, $matches)) {
            require_once '../app/controllers/ResepController.php';
            $controller = new ResepController();
            $controller->toggleTried($matches[1]);
        } elseif (preg_match('/^\/saved\/toggle\/(\d+)$/', $url, $matches)) {
            require_once '../app/controllers/SavedRecipeController.php';
            $controller = new SavedRecipeController();
            $controller->toggle($matches[1]);
        } else {
            // 404 Not Found
            http_response_code(404);
            echo "404 - Page Not Found";
        }
        break;
}
?>
