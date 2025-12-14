<?php
require_once 'includes/header.php';

// Get latest published posts
$stmt = $pdo->prepare("
    SELECT p.*, u.username, c.name as category_name 
    FROM POST p 
    JOIN USERS u ON p.id_user = u.id_user 
    JOIN CATEGORY c ON p.id_category = c.id_category 
    WHERE p.status = 'published' 
    ORDER BY p.creation_date DESC 
    LIMIT 6
");
$stmt->execute();
$latestPosts = $stmt->fetchAll();

// Get popular posts (most viewed)
$stmt = $pdo->prepare("
    SELECT p.*, u.username, c.name as category_name 
    FROM POST p 
    JOIN USERS u ON p.id_user = u.id_user 
    JOIN CATEGORY c ON p.id_category = c.id_category 
    WHERE p.status = 'published' 
    ORDER BY p.view_count DESC 
    LIMIT 4
");
$stmt->execute();
$popularPosts = $stmt->fetchAll();

// Get categories with post count
$stmt = $pdo->query("
    SELECT c.*, COUNT(p.id_post) as post_count 
    FROM CATEGORY c 
    LEFT JOIN POST p ON c.id_category = p.id_category AND p.status = 'published'
    GROUP BY c.id_category
");
$categories = $stmt->fetchAll();
?>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-3">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                Welcome to BlogCMS
                <?php if (Auth::isLoggedIn()): ?>
                    , <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <?php endif; ?>
            </h1>
            <p class="text-gray-600 mb-4">
                A powerful content management system for bloggers and content creators. 
                Share your thoughts, insights, and expertise with the world.
            </p>
            
            <?php if (!Auth::isLoggedIn()): ?>
                <div class="flex space-x-4 mt-4">
                    <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="public/posts.php" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-newspaper mr-2"></i>Browse Articles
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Latest Posts -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-clock mr-2"></i>Latest Articles
                </h2>
                <a href="public/posts.php" class="text-blue-600 hover:text-blue-800">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($latestPosts as $post): ?>
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-4">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mb-2">
                                <?php echo htmlspecialchars($post['category_name']); ?>
                            </span>
                            <h3 class="font-bold text-lg mb-2">
                                <a href="public/view_post.php?id=<?php echo $post['id_post']; ?>" 
                                   class="hover:text-blue-600">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-3">
                                <?php echo substr(strip_tags($post['content']), 0, 100); ?>...
                            </p>
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-user mr-1"></i>
                                    <?php echo htmlspecialchars($post['username']); ?>
                                </span>
                                <!-- <span>
                                    <i class="far fa-calendar mr-1"></i>
                                    <?php //echo formatDate($post['creation_date']); ?>
                                </span> -->
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Categories -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-folder mr-2"></i>Categories
            </h3>
            <ul class="space-y-2">
                <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="public/posts.php?category=<?php echo $category['id_category']; ?>" 
                           class="flex justify-between items-center hover:text-blue-600 p-2 rounded hover:bg-gray-50">
                            <span><?php echo htmlspecialchars($category['name']); ?></span>
                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                <?php echo $category['post_count']; ?>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Popular Posts -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-line mr-2"></i>Popular Posts
            </h3>
            <ul class="space-y-4">
                <?php foreach ($popularPosts as $post): ?>
                    <li class="border-b pb-4 last:border-b-0">
                        <h4 class="font-semibold mb-1">
                            <a href="public/view_post.php?id=<?php echo $post['id_post']; ?>" 
                               class="hover:text-blue-600">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h4>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>
                                <i class="far fa-eye mr-1"></i>
                                <?php echo number_format($post['view_count']); ?>
                            </span>
                            <!-- <span><?php //echo formatDate($post['creation_date']); ?></span> -->
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>