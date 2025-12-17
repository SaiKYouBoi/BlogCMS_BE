<?php
    require_once __DIR__ . '/auth.php';
    include_once __DIR__ . "/config/database.php";
    require_once __DIR__ . '/functions.php';
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
                        <img src="/css/blogcmslogo.png" alt="logo" class="w-[120px]">
                    </a>
                    
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (isLoggedIn()): ?>
                        <span class="hidden text-black md:inline">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <span class="bg-[#8A6F4E] text-white text-xs px-2 py-1 rounded ml-2">
                                <?php echo ucfirst($_SESSION['role']); ?>
                            </span>
                        </span>
                        <a href="/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="/login.php" class="bg-[#8A6F4E] hover:bg-[#6d5538] px-4 py-2 rounded">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>


    <!-- Flash Messages -->
    <div class="container mx-auto px-4 mt-4">
        <?php echo getFlashMessage(); ?>
    </div>

      <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">
    