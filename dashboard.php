<?php
require_once 'includes/header.php';

// redirect if not admin/editor
if (!isAdmin() && !isEditor()) {
    redirect('index.php', 'Access denied!', 'error');
}

//statistics
$totalPosts = getPostCount();
$publishedPosts = getPostCount('published');
$draftPosts = getPostCount('draft');
$totalComments = getCommentCount();
$pendingComments = getCommentCount('pending');
$totalUsers = getUserCount();

//recent posts
$stmt = $pdo->prepare("
    SELECT p.*, u.username, c.name as category_name 
    FROM POST p 
    JOIN USERS u ON p.id_user = u.id_user 
    JOIN CATEGORY c ON p.id_category = c.id_category 
    ORDER BY p.creation_date DESC 
    LIMIT 5
");
$stmt->execute();
$recentPosts = $stmt->fetchAll();

// recent comments
$stmt = $pdo->prepare("
    SELECT c.*, p.title as post_title, u.username 
    FROM COMMENTS c 
    LEFT JOIN POST p ON c.id_post = p.id_post 
    LEFT JOIN USERS u ON c.id_user = u.id_user 
    ORDER BY c.creation_date DESC 
    LIMIT 5
");
$stmt->execute();
$recentComments = $stmt->fetchAll();
?>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-cog mr-2"></i>Admin Menu
            </h3>
            <ul class="space-y-2">
                <li>
                    <a href="dashboard.php" class="flex items-center p-2 bg-[#E6EBDF] text-[#3F4A3E] rounded">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                    <li>
                        <a href="/categories.php" class="flex items-center p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-folder mr-3"></i>Categories
                        </a>
                    </li>
                    <li>
                        <a href="admin/users.php" class="flex items-center p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-users mr-3"></i>Users
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="admin/posts.php" class="flex items-center p-2 hover:bg-gray-100 rounded">
                        <i class="fas fa-newspaper mr-3"></i>All Posts
                    </a>
                </li>
                <li>
                    <a href="admin/comments.php" class="flex items-center p-2 hover:bg-gray-100 rounded">
                        <i class="fas fa-comments mr-3"></i>Comments
                    </a>
                </li>
                <?php if (isAuthor()): ?>
                    <li>
                        <a href="author/my_posts.php" class="flex items-center p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-file-alt mr-3"></i>My Posts
                        </a>
                    </li>
                    <li>
                        <a href="author/create_post.php" class="flex items-center p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-plus-circle mr-3"></i>New Post
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- main Content -->
    <div class="lg:col-span-3">
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </h1>
            <p class="text-gray-600">
                Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                Here's what's happening with your blog today.
            </p>
        </div>

        <!-- statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <!-- posts Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-newspaper text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Posts</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $totalPosts; ?></p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Published</p>
                        <p class="font-bold text-green-600"><?php echo $publishedPosts; ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Drafts</p>
                        <p class="font-bold text-yellow-600"><?php echo $draftPosts; ?></p>
                    </div>
                </div>
            </div>

            <!-- comments card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-comments text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Comments</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $totalComments; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Pending Moderation</p>
                    <p class="font-bold text-red-600"><?php echo $pendingComments; ?></p>
                </div>
            </div>

            <!-- users card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $totalUsers; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Your Role</p>
                    <p class="font-bold text-blue-600"><?php echo ucfirst($_SESSION['role']); ?></p>
                </div>
            </div>
        </div>

        <!-- recent Posts & Comments -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Posts -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-history mr-2"></i>Recent Posts
                </h3>
                <div class="space-y-4">
                    <?php foreach ($recentPosts as $post): ?>
                        <div class="border-b pb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-semibold">
                                        <a href="public/view_post.php?id=<?php echo $post['id_post']; ?>" 
                                           class="hover:text-[#6d5538]">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        By <?php echo htmlspecialchars($post['username']); ?> 
                                        in <?php echo htmlspecialchars($post['category_name']); ?>
                                    </p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded 
                                    <?php echo $post['status'] == 'published' ? 'bg-green-100 text-green-800' : 
                                             ($post['status'] == 'draft' ? 'bg-yellow-100 text-yellow-800' : 
                                             'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </div>
                            <div class="flex text-xs text-gray-500 mt-2 space-x-4">
                                <span>
                                    <i class="far fa-calendar mr-1"></i>
                                    <?php echo formatDate($post['creation_date']); ?>
                                </span>
                                <span>
                                    <i class="far fa-eye mr-1"></i>
                                    <?php echo number_format($post['view_count']); ?> views
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="admin/posts.php" class="text-[#3F4A3E] hover:text-[#3F4A3E] text-sm">
                        View All Posts <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- recent comments -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-comment-dots mr-2"></i>Recent Comments
                </h3>
                <div class="space-y-4">
                    <?php foreach ($recentComments as $comment): ?>
                        <div class="border-b pb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm"><?php echo htmlspecialchars(substr($comment['contenu_commentaire'], 0, 80)); ?>...</p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        On: <?php echo htmlspecialchars($comment['post_title']); ?>
                                        <?php if ($comment['username']): ?>
                                            | By: <?php echo htmlspecialchars($comment['username']); ?>
                                        <?php else: ?>
                                            | By: Guest
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded 
                                    <?php echo $comment['status'] == 'approved' ? 'bg-green-100 text-green-800' : 
                                             ($comment['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                             'bg-red-100 text-red-800'); ?>">
                                    <?php echo ucfirst($comment['status']); ?>
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                <i class="far fa-clock mr-1"></i>
                                <?php echo formatDate($comment['creation_date']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="admin/comments.php" class="text-[#3F4A3E] hover:text-[#3F4A3E] text-sm">
                        View All Comments <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>