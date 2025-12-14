<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    // Login user
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM USERS WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && ($password === $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            return true;
        }
        return false;
    }
    
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Check user role
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }
    
    public static function isEditor() {
        return isset($_SESSION['role']) && ($_SESSION['role'] == 'editor' || $_SESSION['role'] == 'admin');
    }
    
    public static function isAuthor() {
        return isset($_SESSION['role']) && ($_SESSION['role'] == 'author' || $_SESSION['role'] == 'editor' || $_SESSION['role'] == 'admin');
    }
    
    // Get current user ID
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    // Get current user role
    public static function getUserRole() {
        return $_SESSION['role'] ?? 'guest';
    }
    
    // Logout
    public function logout() {
        session_destroy();
        header('Location: ../index.php');
        exit();
    }
    
    // Register new user (for admin)
    public function register($username, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->pdo->prepare("INSERT INTO USERS (username, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword, $role]);
    }
}
// Initialize auth
$auth = new Auth();
?>