<?php
require_once '../includes/header.php';

// redirect if not author
if (!isAuthor()) {
    redirect('../index.php', 'Access denied!', 'error');
}

// get categories
$stmt = $pdo->query("SELECT * FROM CATEGORY ORDER BY name");
$categories = $stmt->fetchAll();

//form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    
    if (empty($title) || empty($content) || empty($category)) {
        $error = "Please fill all required fields!";
    } else {
        if (isset($_POST['id_post'])) {
            // Update existing post
            $id = $_POST['id_post'];
            
            // Check if user owns this post (unless admin/editor)
            $stmt = $pdo->prepare("SELECT id_user FROM POST WHERE id_post = ?");
            $stmt->execute([$id]);
            $post = $stmt->fetch();
            
            if (isAdmin() || isEditor() || $post['id_user'] == getUserId()) {
                $stmt = $pdo->prepare("UPDATE POST SET title = ?, content = ?, id_category = ?, status = ?, update_date = NOW() WHERE id_post = ?");
                $stmt->execute([$title, $content, $category, $status, $id]);
                redirect('my_posts.php', 'Post updated successfully!');
            } else {
                $error = "You can only edit your own posts!";
            }
        } else {
            // Create new post
            $stmt = $pdo->prepare("INSERT INTO POST (title, content, id_category, status, id_user) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $content, $category, $status, getUserId()]);
            $newId = $pdo->lastInsertId();
            redirect('my_posts.php', 'Post created successfully!');
        }
    }
}

// edit mode
$post = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM POST WHERE id_post = ?");
    $stmt->execute([$_GET['edit']]);
    $post = $stmt->fetch();
    
    // check if user can edit this post
    if ($post && !isAdmin() && !isEditor() && $post['id_user'] != getUserId()) {
        redirect('my_posts.php', 'You can only edit your own posts!', 'error');
    }
}
?>
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-<?php echo $post ? 'edit' : 'plus-circle'; ?> mr-2"></i>
        <?php echo $post ? 'Edit Post' : 'Create New Post'; ?>
    </h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-lg shadow p-6">
        <?php if ($post): ?>
            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
        <?php endif; ?>
        
        <!-- title -->
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                Post Title *
            </label>
            <input type="text" id="title" name="title" required
                   value="<?php echo $post ? htmlspecialchars($post['title']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        <!-- category -->
        <div class="mb-6">
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                Category *
            </label>
            <select id="category" name="category" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id_category']; ?>"
                            <?php echo ($post && $post['id_category'] == $category['id_category']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- content -->
        <div class="mb-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                Content *
            </label>
            <textarea id="content" name="content" rows="15" required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"><?php echo $post ? htmlspecialchars($post['content']) : ''; ?></textarea>
            <p class="text-sm text-gray-500 mt-2">
                Basic HTML is allowed. Use &lt;p&gt; for paragraphs, &lt;strong&gt; for bold, &lt;em&gt; for italic.
            </p>
        </div>
        
        <!-- status -->
        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                Status
            </label>
            <select id="status" name="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="draft" <?php echo ($post && $post['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                <option value="published" <?php echo (!$post || $post['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                <option value="archived" <?php echo ($post && $post['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
            </select>
        </div>
        
        <!-- actions -->
        <div class="flex justify-end space-x-4">
            <a href="my_posts.php" 
               class="bg-gray-500 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i>
                <?php echo $post ? 'Update Post' : 'Create Post'; ?>
            </button>
        </div>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>