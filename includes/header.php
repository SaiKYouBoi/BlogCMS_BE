<?php
require_once __DIR__ . '/auth.php';

$pdo = getDBConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogCMS - Content Management System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/blogcms/css/style.css">
    <style>
        .sidebar {
            min-height: calc(100vh - 64px);
        }
        .content {
            min-height: calc(100vh - 64px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-[#E0E5D8] text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="/blogcms/index.php" class="text-2xl text-[#121212] font-bold">
                        <i class="fas text-[28px] fa-blog mr-2"></i>BlogCMS
                    </a>
                    <div class="hidden md:flex space-x-4">
                        
                        <?php if (Auth::isLoggedIn()): ?>
                            <?php if (Auth::isAdmin() || Auth::isEditor()): ?>
                                <a href="/blogcms/dashboard.php" class="hover:text-blue-200">
                                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                                </a>
                            <?php endif; ?>
                            <?php if (Auth::isAuthor()): ?>
                                <a href="/blogcms/author/my_posts.php" class="hover:text-blue-200">
                                    <i class="fas fa-file-alt mr-1"></i> My Posts
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (Auth::isLoggedIn()): ?>
                        <span class="hidden md:inline">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <span class="bg-blue-500 text-xs px-2 py-1 rounded ml-2">
                                <?php echo ucfirst($_SESSION['role']); ?>
                            </span>
                        </span>
                        <a href="/blogcms/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="/blogcms/login.php" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <div class="container mx-auto px-4 mt-4">
        <?php //echo getFlashMessage(); ?>
    </div>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">