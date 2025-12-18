<?php
require_once '../includes/header.php';

// redirect if not admin/editor
if (!isAdmin() && !isEditor()) {
    redirect('../index.php', 'Access denied!', 'error');
}

// handle comment actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_comment'])) {
        $id = $_POST['id_comment'];
        $stmt = $pdo->prepare("UPDATE COMMENTS SET status = 'approved' WHERE id_comment = ?");
        $stmt->execute([$id]);
        redirect('comments.php', 'Comment approved!');
    }
    
    if (isset($_POST['reject_comment'])) {
        $id = $_POST['id_comment'];
        $stmt = $pdo->prepare("UPDATE COMMENTS SET status = 'spam' WHERE id_comment = ?");
        $stmt->execute([$id]);
        redirect('comments.php', 'Comment marked as spam!');
    }
    
    if (isset($_POST['delete_comment'])) {
        $id = $_POST['id_comment'];
        $stmt = $pdo->prepare("DELETE FROM COMMENTS WHERE id_comment = ?");
        $stmt->execute([$id]);
        redirect('comments.php', 'Comment deleted!');
    }
}

// get filter
$filter = $_GET['filter'] ?? 'all';

// build query based on filter
$query = "SELECT c.*, p.title as post_title, u.username 
          FROM COMMENTS c 
          LEFT JOIN POST p ON c.id_post = p.id_post 
          LEFT JOIN USERS u ON c.id_user = u.id_user";
          
if ($filter != 'all') {
    $query .= " WHERE c.status = ?";
}

$query .= " ORDER BY c.creation_date DESC";

$stmt = $pdo->prepare($query);
if ($filter != 'all') {
    $stmt->execute([$filter]);
} else {
    $stmt->execute();
}
$comments = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-comments mr-2"></i>Comments Management
    </h1>

    <!-- Comments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($comments as $comment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                    <?php echo htmlspecialchars($comment['contenu_commentaire']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php if ($comment['username']): ?>
                                        <?php echo htmlspecialchars($comment['username']); ?>
                                    <?php else: ?>
                                        <span class="text-gray-500">Guest</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <a href="../visitor/view_post.php?id=<?php echo $comment['id_post']; ?>" 
                                       class="text-[#7A8F6A] hover:text-[#3F5B45]">
                                        <?php echo htmlspecialchars($comment['post_title']); ?>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatDate($comment['creation_date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php echo $comment['status'] == 'approved' ? 'bg-green-100 text-green-800' : 
                                             ($comment['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                             'bg-red-100 text-red-800'); ?>">
                                    <?php echo ucfirst($comment['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($comment['status'] == 'pending'): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id_comment" value="<?php echo $comment['id_comment']; ?>">
                                        <button type="submit" name="approve_comment" 
                                                class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($comment['status'] != 'spam'): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id_comment" value="<?php echo $comment['id_comment']; ?>">
                                        <button type="submit" name="reject_comment" 
                                                class="text-red-600 hover:text-red-900 mr-3">
                                            <i class="fas fa-ban"></i> Spam
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                    <input type="hidden" name="id_comment" value="<?php echo $comment['id_comment']; ?>">
                                    <button type="submit" name="delete_comment" 
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
    </div>
    
    <?php if (empty($comments)): ?>
        <div class="text-center py-12">
            <i class="fas fa-comment-slash text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">No comments found</p>
        </div>
    <?php endif; ?>
</div>
<?php require_once '../includes/footer.php'; ?>