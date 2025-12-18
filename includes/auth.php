<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// connect to database
$pdo = getDBConnection();

//login
function login($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM USERS WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        return true;
    }
    return false;
}

//check logiin
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

//role chekcs
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isEditor() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['editor', 'admin']);
}

function isAuthor() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['author', 'editor', 'admin']);
}

//user info
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserRole(): mixed {
    return $_SESSION['role'] ?? 'guest';
}


function logout() {
    session_destroy();
    header('Location: ../index.php');
    exit();
}


function register($username, $email, $password, $role) {
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO USERS (username, email, password, role) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$username, $email, $hashedPassword, $role]);
}
?>
