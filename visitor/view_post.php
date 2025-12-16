<?php
require_once '../includes/header.php';

// post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('posts.php', 'Post not found!', 'error');
}

$postId = (int)$_GET['id'];

// Get post
$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.email, c.name as category_name 
    FROM POST p 
    JOIN USERS u ON p.id_user = u.id_user 
    JOIN CATEGORY c ON p.id_category = c.id_category 
    WHERE p.id_post = ? AND p.status = 'published'
");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    redirect('posts.php', 'Post not found or not published!', 'error');
}

// Increment view count
$stmt = $pdo->prepare("UPDATE POST SET view_count = view_count + 1 WHERE id_post = ?");
$stmt->execute([$postId]);

// comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_comment'])) {
    $content = sanitize($_POST['comment_content']);
    $userId = isLoggedIn() ? getUserId() : null;
    
    if (empty($content)) {
        $commentError = "Comment cannot be empty!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO COMMENTS (contenu_commentaire, id_user, id_post, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$content, $userId, $postId]);
        $commentSuccess = "Comment submitted! It will be visible after moderation.";
    }
}

$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM COMMENTS c 
    LEFT JOIN USERS u ON c.id_user = u.id_user 
    WHERE c.id_post = ? AND c.status = 'approved' 
    ORDER BY c.creation_date DESC
");
$stmt->execute([$postId]);
$comments = $stmt->fetchAll();

?>
<div class="max-w-4xl mx-auto">
    <!-- Post Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded mb-2">
                    <?php echo htmlspecialchars($post['category_name']); ?>
                </span>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
            </div>
            <?php if (isAdmin() || isEditor() || (isAuthor() && $post['id_user'] == getUserId())): ?>
                <a href="../author/create_post.php?edit=<?php echo $post['id_post']; ?>" 
                   class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i> Edit
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Post Meta -->
        <div class="flex flex-wrap items-center text-gray-600 text-sm mb-6 space-x-4">
            <span>
                <i class="fas fa-user mr-1"></i>
                By <?php echo htmlspecialchars($post['username']); ?>
            </span>
            <span>
                <i class="far fa-calendar mr-1"></i>
                <?php echo formatDate($post['creation_date']); ?>
            </span>
            <span>
                <i class="far fa-eye mr-1"></i>
                <?php echo number_format($post['view_count']); ?> views
            </span>
            <?php if ($post['update_date'] != $post['creation_date']): ?>
                <span class="text-gray-500">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Updated <?php echo formatDate($post['update_date']); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Post Content -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="prose max-w-none">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-comments mr-2"></i>
            Comments (<?php echo count($comments); ?>)
        </h3>

        <?php if (isset($commentSuccess)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $commentSuccess; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($commentError)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $commentError; ?>
            </div>
        <?php endif; ?>

        <!-- Comment Form -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-700 mb-3">Leave a Comment</h4>
            <form method="POST">
                <textarea name="comment_content" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-3"
                          placeholder="Write your comment here..."></textarea>
                <div class="flex justify-between items-center">
                    <?php if (!isLoggedIn()): ?>
                        <p class="text-sm text-gray-600">
                            Your comment will be posted as "Guest"
                        </p>
                    <?php endif; ?>
                    <button type="submit" name="post_comment" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-paper-plane mr-2"></i>Post Comment
                    </button>
                </div>
            </form>
        </div>

        <!-- Comments List -->
        <?php if (!empty($comments)): ?>
            <div class="space-y-6">
                <?php foreach ($comments as $comment): ?>
                    <div class="border-b pb-6 last:border-b-0">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-semibold">
                                <?php if ($comment['username']): ?>
                                    <?php echo htmlspecialchars($comment['username']); ?>
                                <?php else: ?>
                                    <span class="text-gray-500">Guest</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-sm text-gray-500">
                                <?php echo formatDate($comment['creation_date']); ?>
                            </span>
                        </div>
                        <p class="text-gray-700">
                            <?php echo htmlspecialchars($comment['contenu_commentaire']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-comment-slash text-3xl mb-3"></i>
                <p>No comments yet. Be the first to comment!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>