<?php
/**
 * Admin - Categories List
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Manage Categories';

$categoryModel = new Category();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (verifyCSRFToken($_GET['token'] ?? '')) {
        $categoryModel->delete((int)$_GET['delete']);
        setFlash('success', 'Category deleted successfully.');
        redirect(ADMIN_URL . '/categories/');
    }
}

$categories = $categoryModel->getAll(true);

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
            <p class="text-gray-500">Manage coupon and article categories</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/categories/add.php" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Category
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Coupons</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Stores</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                        <?php if ($category['icon']): ?>
                                        <i class="fas <?php echo htmlspecialchars($category['icon']); ?> text-primary"></i>
                                        <?php else: ?>
                                        <i class="fas fa-folder text-gray-400"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($category['name']); ?></div>
                                        <div class="text-sm text-gray-500">/category/<?php echo htmlspecialchars($category['slug']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm"><?php echo $category['coupon_count']; ?></td>
                            <td class="px-6 py-4 text-sm"><?php echo $category['store_count']; ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $category['status'] === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'; ?>">
                                    <?php echo ucfirst($category['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="<?php echo BASE_URL; ?>/category/<?php echo $category['slug']; ?>" target="_blank" class="p-2 text-gray-500 hover:text-blue-600"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo ADMIN_URL; ?>/categories/edit.php?id=<?php echo $category['id']; ?>" class="p-2 text-gray-500 hover:text-green-600"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo ADMIN_URL; ?>/categories/?delete=<?php echo $category['id']; ?>&token=<?php echo generateCSRFToken(); ?>" 
                                       class="p-2 text-gray-500 hover:text-red-600" onclick="return confirm('Delete this category? Associated coupons/stores will be uncategorized.');"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No categories found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
