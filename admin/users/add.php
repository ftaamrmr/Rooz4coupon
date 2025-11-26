<?php
/**
 * Admin - Add User
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/User.php';

requireRole(['admin']);

$pageTitle = 'Add User';

$userModel = new User();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $data = [
        'username' => sanitize($_POST['username'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'full_name' => sanitize($_POST['full_name'] ?? ''),
        'role' => $_POST['role'] ?? 'writer',
        'status' => $_POST['status'] ?? 'active'
    ];
    
    if (empty($data['username'])) {
        $errors[] = 'Username is required';
    } elseif ($userModel->usernameExists($data['username'])) {
        $errors[] = 'Username already exists';
    }
    
    if (empty($data['email'])) {
        $errors[] = 'Email is required';
    } elseif (!isValidEmail($data['email'])) {
        $errors[] = 'Invalid email address';
    } elseif ($userModel->emailExists($data['email'])) {
        $errors[] = 'Email already exists';
    }
    
    if (empty($data['password'])) {
        $errors[] = 'Password is required';
    } elseif (strlen($data['password']) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if (empty($errors)) {
        $userId = $userModel->create($data);
        if ($userId) {
            logActivity('user_create', 'Created user: ' . $data['username']);
            setFlash('success', 'User created successfully.');
            redirect(ADMIN_URL . '/users/');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Add User</h1>
            <p class="text-gray-500">Create a new admin user</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/users/" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Users
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" class="max-w-2xl">
        <?php echo csrfField(); ?>
        
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="writer">Writer (Articles only)</option>
                        <option value="editor">Editor (Content management)</option>
                        <option value="admin">Admin (Full access)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Create User
                </button>
            </div>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
