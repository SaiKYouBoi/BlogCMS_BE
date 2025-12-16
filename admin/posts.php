<?php
require_once '../includes/header.php';

// Redirect if not admin/editor
if (!isAdmin() && !isEditor()) {
    redirect('../index.php', 'Access denied!', 'error');
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_post'])) {
    $id = $_POST['id_post'];
    
    // Check if current user is author or admin
    $stmt = $pdo->prepare("SELECT id_user FROM POST WHERE id_post = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    if (isAdmin() || (isEditor() && $post['id_user'] == getUserId())) {
        $stmt = $pdo->prepare("DELETE FROM POST WHERE id_post = ?");
        $stmt->execute([$id]);
        redirect('posts.php', 'Post deleted successfully!');
    } else {
        redirect('posts.php', 'You can only delete your own posts!', 'error');
    }
}

// Build query
$query = "SELECT p.*, u.username, c.name as category_name 
          FROM POST p 
          JOIN USERS u ON p.id_user = u.id_user 
          JOIN CATEGORY c ON p.id_category = c.id_category 
          WHERE 1=1";

$query .= " ORDER BY p.creation_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();

?>
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-newspaper mr-2"></i>All Posts
    </h1>
    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($posts as $post): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="../public/view_post.php?id=<?php echo $post['id_post']; ?>" 
                                       class="hover:text-blue-600">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($post['username']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($post['category_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php echo $post['status'] == 'published' ? 'bg-green-100 text-green-800' : 
                                             ($post['status'] == 'draft' ? 'bg-yellow-100 text-yellow-800' : 
                                             'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo number_format($post['view_count']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatDate($post['creation_date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="../author/create_post.php?edit=<?php echo $post['id_post']; ?>" 
                                   class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (isAdmin() || $post['id_user'] == getUserId()): ?>
                                    <form method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                                        <button type="submit" name="delete_post" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>