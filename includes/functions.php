<?php

function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        session_start();
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}


function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        
        $colors = [
            'success' => 'green',
            'error' => 'red',
            'warning' => 'yellow',
            'info' => 'blue'
        ];
        $color = $colors[$type] ?? 'blue';
        
        return "<div class='bg-{$color}-100 border border-{$color}-400 text-{$color}-700 px-4 py-3 rounded mb-4'>{$message}</div>";
    }
    return '';
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}


function getPostCount($status = null) {
    global $pdo;
    $sql = "SELECT COUNT(*) as count FROM POST";
    if ($status) {
        $sql .= " WHERE status = ?";
        $stmt = $pdo->prepare(query: $sql);
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query($sql);
    }
    return $stmt->fetch()['count'];

}

function getCommentCount($status = null) {
    global $pdo;
    $sql = "SELECT COUNT(*) as count FROM COMMENTS";
    if ($status) {
        $sql .= " WHERE status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query($sql);
    }
    return $stmt->fetch()['count'];
}

function getUserCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM USERS");
    return $stmt->fetch()['count'];
}
?>