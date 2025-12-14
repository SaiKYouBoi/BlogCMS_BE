<?php
// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Redirect with message
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Get flash message
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

// Format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Get category name by ID
function getCategoryName($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM CATEGORY WHERE id_category = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ? $result['name'] : 'Uncategorized';
}

// Get post count
function getPostCount($status = null) {
    global $pdo;
    $sql = "SELECT COUNT(*) as count FROM POST";
    if ($status) {
        $sql .= " WHERE status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query($sql);
    }
    return $stmt->fetch()['count'];
}

// Get comment count
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

// Get user count
function getUserCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM USERS");
    return $stmt->fetch()['count'];
}
?>