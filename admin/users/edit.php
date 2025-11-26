<?php
/**
 * Admin - Edit User
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/User.php';

requireRole(['admin']);

$pageTitle = 'Edit User';

$userModel = new User();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = $userModel->getById($id);

if (!$user) {
    setFlash('error', 'User not found.');
    redirect(ADMIN_URL . '/users/');
}

// Prevent editing your own role if you're the only admin
if ($user['id'] == $_SESSION['user_id'] && $user['role'] === 'admin') {
    $adminCount = count(array_filter($userModel->getAll(['role' => 'admin']), function($u) {
        return $u['status'] === 'active';
    }));
    $isOnlyAdmin = $adminCount <= 1;
} else {
    $isOnlyAdmin = false;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $data = [
        'username' => sanitize($_POST['username'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'full_name' => sanitize($_POST['full_name'] ?? ''),
        'role' => $_POST['role'] ?? 'writer',
        'status' => $_POST['status'] ?? 'active'
    ];
    
    // Validation
    if (empty($data['username'])) {
        $errors[] = 'Username is required';
    } elseif ($userModel->usernameExists($data['username'], $id)) {
        $errors[] = 'Username already exists';
    }
    
    if (empty($data['email'])) {
        $errors[] = 'Email is required';
    } elseif (!isValidEmail($data['email'])) {
        $errors[] = 'Invalid email format';
    } elseif ($userModel->emailExists($data['email'], $id)) {
        $errors[] = 'Email already exists';
    }
    
    // Prevent removing admin role if only admin
    if ($isOnlyAdmin && $data['role'] !== 'admin') {
        $errors[] = 'Cannot change role - you are the only admin';
    }
    
    // Prevent deactivating only admin
    if ($isOnlyAdmin && $data['status'] !== 'active') {
        $errors[] = 'Cannot deactivate - you are the only admin';
    }
    
    // Handle password change
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirm'] ?? '';
    
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
    }
    
    // Handle avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $upload = uploadFile($_FILES['avatar'], 'uploads/avatars');
        if ($upload['success']) {
            if ($user['avatar']) {
                deleteFile($user['avatar']);
            }
            $data['avatar'] = $upload['path'];
        } else {
            $errors[] = 'Avatar upload failed: ' . $upload['error'];
        }
    }
    
    if (empty($errors)) {
        if ($userModel->update($id, $data)) {
            // Update password if provided
            if (!empty($newPassword)) {
                $userModel->updatePassword($id, $newPassword);
            }
            
            logActivity('user_update', 'Updated user: ' . $data['username']);
            setFlash('success', 'User updated successfully.');
            redirect(ADMIN_URL . '/users/');
        } else {
            $errors[] = 'Failed to update user. Please try again.';
        }
    }
    
    $user = array_merge($user, $data);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
            <p class="text-gray-500">Update user account details</p>
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
    
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrfField(); ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Account Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Account Information</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                
                <!-- Password Change -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Change Password</h2>
                    <p class="text-sm text-gray-500 mb-4">Leave blank to keep current password</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Minimum 6 characters">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirm"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Re-enter password">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Role & Status -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Role & Status</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    <?php echo $isOnlyAdmin ? 'disabled' : ''; ?>>
                                <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="editor" <?php echo ($user['role'] ?? '') === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                <option value="writer" <?php echo ($user['role'] ?? '') === 'writer' ? 'selected' : ''; ?>>Writer</option>
                            </select>
                            <?php if ($isOnlyAdmin): ?>
                            <input type="hidden" name="role" value="admin">
                            <p class="text-xs text-yellow-600 mt-1">Cannot change - only admin</p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    <?php echo $isOnlyAdmin ? 'disabled' : ''; ?>>
                                <option value="active" <?php echo ($user['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($user['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                            <?php if ($isOnlyAdmin): ?>
                            <input type="hidden" name="status" value="active">
                            <p class="text-xs text-yellow-600 mt-1">Cannot deactivate - only admin</p>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Update User
                        </button>
                    </div>
                </div>
                
                <!-- Avatar -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Avatar</h2>
                    
                    <div class="text-center mb-4">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo UPLOAD_URL . '/' . $user['avatar']; ?>" alt="Avatar" class="w-24 h-24 mx-auto rounded-full object-cover">
                        <?php else: ?>
                        <div class="w-24 h-24 mx-auto rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-4xl text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        <label for="avatar" class="cursor-pointer">
                            <div id="avatar-preview" class="hidden mb-2">
                                <img src="" alt="Preview" class="w-16 h-16 mx-auto rounded-full object-cover">
                            </div>
                            <i class="fas fa-camera text-2xl text-gray-400 mb-1"></i>
                            <p class="text-sm text-gray-500">Upload new avatar</p>
                        </label>
                    </div>
                </div>
                
                <!-- Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Account Info</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Last Login:</span>
                            <span class="font-medium"><?php echo $user['last_login'] ? formatDate($user['last_login'], 'M d, Y H:i') : 'Never'; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Registered:</span>
                            <span class="font-medium"><?php echo formatDate($user['created_at'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    const img = preview.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
