<?php
require_once '../app/core/Controller.php';
require_once '../app/models/User.php';

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $this->redirect('/');
            } else {
                $error = "Username atau password salah!";
                $this->view('auth/login', ['error' => $error]);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validasi
            $errors = [];
            
            if (empty($username) || empty($email) || empty($password)) {
                $errors[] = "Semua field harus diisi!";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Password tidak cocok!";
            }
            
            if ($this->userModel->findByUsername($username)) {
                $errors[] = "Username sudah digunakan!";
            }
            
            if ($this->userModel->findByEmail($email)) {
                $errors[] = "Email sudah digunakan!";
            }

            if (empty($errors)) {
                if ($this->userModel->register($username, $email, $password)) {
                    $success = "Registrasi berhasil! Silakan login.";
                    $this->view('auth/login', ['success' => $success]);
                } else {
                    $errors[] = "Terjadi kesalahan saat registrasi!";
                    $this->view('auth/register', ['errors' => $errors]);
                }
            } else {
                $this->view('auth/register', ['errors' => $errors]);
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/auth/login');
    }
}
?>
