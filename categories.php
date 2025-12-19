<?php
require_once __DIR__ . "/includes/header.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        $stmt = $pdo->prepare("INSERT INTO CATEGORY (name, description) VALUES (?, ?)");
        if ($stmt->execute([$name, $description])) {
            redirect('categories.php', 'Category added successfully!');
        } else {
            redirect('categories.php', 'Error adding category!', 'error');
        }
    }
    
    if (isset($_POST['edit_category'])) {
        $id = $_POST['id_category'];
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        $stmt = $pdo->prepare("UPDATE CATEGORY SET name = ?, description = ? WHERE id_category = ?");
        if ($stmt->execute([$name, $description, $id])) {
            redirect('categories.php', 'Category updated successfully!');
        } else {
            redirect('categories.php', 'Error updating category!', 'error');
        }
    }
    
    if (isset($_POST['delete_category'])) {
        $id = $_POST['id_category'];
        
        // check if category has posts
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM POST WHERE id_category = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            redirect('categories.php', 'Cannot delete category with posts!', 'error');
        } else {
            $stmt = $pdo->prepare("DELETE FROM CATEGORY WHERE id_category = ?");
            if ($stmt->execute([$id])) {
                redirect('categories.php', 'Category deleted successfully!');
            } else {
                redirect('categories.php', 'Error deleting category!', 'error');
            }
        }
    }
}

// get all categories
$stmt = $pdo->query("SELECT c.*, COUNT(p.id_post) as post_count 
                     FROM CATEGORY c 
                     LEFT JOIN POST p ON c.id_category = p.id_category 
                     GROUP BY c.id_category 
                     ORDER BY c.name");
$categories = $stmt->fetchAll();

//edit mode
$editCategory = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM CATEGORY WHERE id_category = ?");
    $stmt->execute([$_GET['edit']]);
    $editCategory = $stmt->fetch();
}
?>

<div class="max-w-7xl mx-auto mt-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-folder mr-2"></i>Categories Management
        </h1>
        <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" 
                class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Add Category
        </button>
    </div>

    <!-- categories table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($categories as $category): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $category['id_category']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <?php echo htmlspecialchars(substr($category['description'], 0, 100)); ?>
                                    <?php if (strlen($category['description']) > 100): ?>...<?php endif; ?>
                                </div>
                            </td>
                           
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="?edit=<?php echo $category['id_category']; ?>" 
                                   class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="confirmDelete(<?php echo $category['id_category']; ?>)" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- add/edit Category Modal -->
<div id="addCategoryModal" class="<?php echo $editCategory ? '' : 'hidden'; ?> fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?>
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST">
            <?php if ($editCategory): ?>
                <input type="hidden" name="id_category" value="<?php echo $editCategory['id_category']; ?>">
            <?php endif; ?>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Category Name
                </label>
                <input type="text" id="name" name="name" required
                       value="<?php echo $editCategory ? htmlspecialchars($editCategory['name']) : ''; ?>"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea id="description" name="description" rows="3"
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo $editCategory ? htmlspecialchars($editCategory['description']) : ''; ?></textarea>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
                <?php if ($editCategory): ?>
                    <button type="submit" name="edit_category"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                <?php else: ?>
                    <button type="submit" name="add_category"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i>Add
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- delete confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Category</h3>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to delete this category? This action cannot be undone.
            </p>
            <form id="deleteForm" method="POST">
                <input type="hidden" name="id_category" id="deleteId">
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" name="delete_category"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('addCategoryModal').classList.add('hidden');
    window.history.replaceState({}, document.title, window.location.pathname);
}

function confirmDelete(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

window.onclick = function(event) {
    const addModal = document.getElementById('addCategoryModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target == addModal) {
        closeModal();
    }
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}
</script>
<?php require_once '../includes/footer.php'; ?>