<?php
/**
 * Admin - Stores List
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Store.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Manage Stores';

$storeModel = new Store();
$categoryModel = new Category();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (verifyCSRFToken($_GET['token'] ?? '')) {
        $storeModel->delete((int)$_GET['delete']);
        setFlash('success', 'Store deleted successfully.');
        redirect(ADMIN_URL . '/stores/');
    }
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$filters = ['search' => $_GET['search'] ?? null];
$stores = $storeModel->getAll($page, ADMIN_ITEMS_PER_PAGE, array_filter($filters));
$totalStores = $storeModel->getCount(array_filter($filters));
$pagination = paginate($totalStores, ADMIN_ITEMS_PER_PAGE, $page, '/admin/stores/');

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Stores</h1>
            <p class="text-gray-500">Manage all stores</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/stores/add.php" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Store
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Coupons</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (!empty($stores)): ?>
                        <?php foreach ($stores as $store): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden mr-3">
                                        <?php if ($store['logo']): ?>
                                        <img src="<?php echo UPLOAD_URL . '/' . $store['logo']; ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                        <span class="font-bold text-gray-400"><?php echo strtoupper(substr($store['name'], 0, 1)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($store['name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo number_format($store['views_count'] ?? 0); ?> views</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($store['category_name'] ?? 'Uncategorized'); ?></td>
                            <td class="px-6 py-4 text-sm"><?php echo $store['coupon_count']; ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $store['status'] === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'; ?>">
                                    <?php echo ucfirst($store['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="<?php echo BASE_URL; ?>/store/<?php echo $store['slug']; ?>" target="_blank" class="p-2 text-gray-500 hover:text-blue-600"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo ADMIN_URL; ?>/stores/edit.php?id=<?php echo $store['id']; ?>" class="p-2 text-gray-500 hover:text-green-600"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo ADMIN_URL; ?>/stores/?delete=<?php echo $store['id']; ?>&token=<?php echo generateCSRFToken(); ?>" 
                                       class="p-2 text-gray-500 hover:text-red-600" onclick="return confirm('Delete this store?');"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No stores found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
