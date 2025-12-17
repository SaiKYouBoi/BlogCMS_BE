<?php
require_once 'includes/header.php';

$stmt = $pdo->prepare("
    SELECT p.*, u.username, c.name as category_name 
    FROM POST p 
    JOIN USERS u ON p.id_user = u.id_user 
    JOIN CATEGORY c ON p.id_category = c.id_category 
");

$stmt->execute();
$Posts = $stmt->fetchAll();

?>
<?php if (isLoggedIn()): ?>
<?php if (isAdmin() || isEditor()): ?>
<div class="w-[90%] m-auto mt-6 flex flex-row gap-4">
    <a href="/categories.php"
   class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-5 py-2 rounded-md font-medium shadow-sm transition duration-200">
    <i class="fas fa-newspaper mr-1"></i> 
    Categories
    </a>
<?php endif; ?>

<?php if (isAdmin() || isEditor()): ?>
    <a href="/dashboard.php"
   class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-5 py-2 rounded-md font-medium shadow-sm transition duration-200">
    <i class="fas fa-tachometer-alt mr-1"></i>
    Dashboard
    </a>
<?php endif; ?>
<?php if (isAuthor()): ?>
    <a href="/categories.php"
   class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-5 py-2 rounded-md font-medium shadow-sm transition duration-200">
    <i class="fas fa-file-alt mr-1"></i>
    My Posts
    </a>
<?php endif; ?>
<?php endif; ?>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-3">
        
        <!-- Posts -->
        <div class="bg-white rounded-lg w-[90%] shadow p-6 m-auto mt-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    Articles
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($Posts as $post): ?>
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-4">
                            <span class="inline-block bg-[#E6EBDF] text-[#3F4A3E] text-xs px-2 py-1 rounded mb-2">
                                <?php echo htmlspecialchars($post['category_name']); ?>
                            </span>
                            <h3 class="font-bold text-lg mb-2">
                                <a href="/visitor/view_post.php?id=<?php echo $post['id_post']; ?>" 
                                   class="hover:text-[#6d5538]">
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
                                
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
    <?php require_once 'includes/footer.php'; ?>