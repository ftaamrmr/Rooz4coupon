<?php
/**
 * Admin - Users List
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/User.php';

requireRole(['admin']);

$pageTitle = 'Manage Users';

$userModel = new User();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (verifyCSRFToken($_GET['token'] ?? '')) {
        if ((int)$_GET['delete'] !== $_SESSION['user_id']) {
            $userModel->delete((int)$_GET['delete']);
            setFlash('success', 'User deleted successfully.');
        } else {
            setFlash('error', 'You cannot delete your own account.');
        }
        redirect(ADMIN_URL . '/users/');
    }
}

$users = $userModel->getAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Users</h1>
            <p class="text-gray-500">Manage admin users and roles</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/users/add.php" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Add User
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                    <?php if ($user['avatar']): ?>
                                    <img src="<?php echo UPLOAD_URL . '/' . $user['avatar']; ?>" alt="" class="w-full h-full rounded-full object-cover">
                                    <?php else: ?>
                                    <i class="fas fa-user text-gray-500"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></div>
                                    <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($user['username']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                <?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-600' : 
                                           ($user['role'] === 'editor' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm"><?php echo $user['last_login'] ? timeAgo($user['last_login']) : 'Never'; ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="<?php echo ADMIN_URL; ?>/users/edit.php?id=<?php echo $user['id']; ?>" class="p-2 text-gray-500 hover:text-green-600"><i class="fas fa-edit"></i></a>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                <a href="<?php echo ADMIN_URL; ?>/users/?delete=<?php echo $user['id']; ?>&token=<?php echo generateCSRFToken(); ?>" 
                                   class="p-2 text-gray-500 hover:text-red-600" onclick="return confirm('Delete this user?');"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
