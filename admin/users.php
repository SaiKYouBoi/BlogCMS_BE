<?php
require_once '../includes/header.php';

// Redirect if not admin
if (!isAdmin()) {
    redirect('../index.php', 'Access denied!', 'error');
}

// Handle user operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize($_POST['role']);
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id_user FROM USERS WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Email already exists!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO USERS (username, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashedPassword, $role])) {
                redirect('users.php', 'User added successfully!');
            } else {
                $error = "Error adding user!";
            }
        }
    }
    
    if (isset($_POST['update_user'])) {
        $id = $_POST['id_user'];
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $role = sanitize($_POST['role']);
        
        // Check if email exists for another user
        $stmt = $pdo->prepare("SELECT id_user FROM USERS WHERE email = ? AND id_user != ?");
        $stmt->execute([$email, $id]);
        
        if ($stmt->fetch()) {
            $error = "Email already exists for another user!";
        } else {
            $stmt = $pdo->prepare("UPDATE USERS SET username = ?, email = ?, role = ? WHERE id_user = ?");
            if ($stmt->execute([$username, $email, $role, $id])) {
                redirect('users.php', 'User updated successfully!');
            } else {
                $error = "Error updating user!";
            }
        }
    }
    
    if (isset($_POST['delete_user'])) {
        $id = $_POST['id_user'];
        
        // Don't allow deleting yourself
        if ($id == getUserId()) {
            redirect('users.php', 'You cannot delete your own account!', 'error');
        }
        
        $stmt = $pdo->prepare("DELETE FROM USERS WHERE id_user = ?");
        if ($stmt->execute([$id])) {
            redirect('users.php', 'User deleted successfully!');
        } else {
            $error = "Error deleting user!";
        }
    }
}

// Get all users
$stmt = $pdo->query("SELECT * FROM USERS ORDER BY inscription_date DESC");
$users = $stmt->fetchAll();

// For edit mode
$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM USERS WHERE id_user = ?");
    $stmt->execute([$_GET['edit']]);
    $editUser = $stmt->fetch();
}
?>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-users mr-2"></i>Users Management
        </h1>
        <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" 
                class="bg-[#8A6F4E] hover:bg-[#6d5538] text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Add User
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $user['id_user']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                    <?php if ($user['id_user'] == getUserId()): ?>
                                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php echo $user['role'] == 'admin' ? 'bg-red-100 text-red-800' : 
                                             ($user['role'] == 'editor' ? 'bg-purple-100 text-purple-800' :
                                             ($user['role'] == 'author' ? 'bg-green-100 text-green-800' :
                                             'bg-gray-100 text-gray-800')); ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatDate($user['inscription_date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="?edit=<?php echo $user['id_user']; ?>" 
                                   class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($user['id_user'] != getUserId()): ?>
                                    <button onclick="confirmDelete(<?php echo $user['id_user']; ?>)" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="addUserModal" class="<?php echo $editUser ? '' : 'hidden'; ?> fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">
                <?php echo $editUser ? 'Edit User' : 'Add New User'; ?>
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <?php if ($editUser): ?>
                <input type="hidden" name="id_user" value="<?php echo $editUser['id_user']; ?>">
            <?php endif; ?>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input type="text" id="username" name="username" required
                       value="<?php echo $editUser ? htmlspecialchars($editUser['username']) : ''; ?>"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input type="email" id="email" name="email" required
                       value="<?php echo $editUser ? htmlspecialchars($editUser['email']) : ''; ?>"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <?php if (!$editUser): ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input type="password" id="password" name="password" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            <?php endif; ?>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                    Role    
                </label>
                <select id="role" name="role" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="subscriber" <?php echo ($editUser && $editUser['role'] == 'subscriber') ? 'selected' : ''; ?>>Subscriber</option>
                    <option value="author" <?php echo ($editUser && $editUser['role'] == 'author') ? 'selected' : ''; ?>>Author</option>
                    <option value="editor" <?php echo ($editUser && $editUser['role'] == 'editor') ? 'selected' : ''; ?>>Editor</option>
                    <option value="admin" <?php echo ($editUser && $editUser['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
                <?php if ($editUser): ?>
                    <button type="submit" name="update_user"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                <?php else: ?>
                    <button type="submit" name="add_user"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i>Add
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete User</h3>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to delete this user? This action cannot be undone.
            </p>
            <form id="deleteForm" method="POST">
                <input type="hidden" name="id_user" id="deleteId">
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" name="delete_user"
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
    document.getElementById('addUserModal').classList.add('hidden');
    window.history.replaceState({}, document.title, window.location.pathname);
}

function confirmDelete(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addUserModal');
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