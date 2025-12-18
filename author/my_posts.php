<?php
require_once '../includes/header.php';

// redirect if not author
if (!isAuthor()) {
    redirect('../index.php', 'Access denied!', 'error');
}

// get author's posts
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM POST p 
    JOIN CATEGORY c ON p.id_category = c.id_category 
    WHERE p.id_user = ? 
    ORDER BY p.creation_date DESC
");
$stmt->execute([getUserId()]);
$posts = $stmt->fetchAll();
?>
    <div class="max-w-7xl mx-auto h-[65vh]">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-file-alt mr-2"></i>My Posts
        </h1>
        <a href="create_post.php" 
           class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>New Post
        </a>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
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
                                <a href="create_post.php?edit=<?php echo $post['id_post']; ?>" 
                                   class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="../admin/posts.php" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this post?');">
                                    <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                                    <button type="submit" name="delete_post" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($posts)): ?>
            <div class="text-center py-12">
                <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 mb-4">You haven't created any posts yet.</p>
                <a href="create_post.php" 
                   class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>Create Your First Post
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>