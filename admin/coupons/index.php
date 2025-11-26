<?php
/**
 * Admin - Coupons List
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Coupon.php';
require_once APP_PATH . '/models/Store.php';

requireAuth();

$pageTitle = 'Manage Coupons';

$couponModel = new Coupon();
$storeModel = new Store();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (verifyCSRFToken($_GET['token'] ?? '')) {
        $couponModel->delete((int)$_GET['delete']);
        setFlash('success', 'Coupon deleted successfully.');
        redirect(ADMIN_URL . '/coupons/');
    }
}

// Pagination and filters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$filters = [
    'status' => $_GET['status'] ?? null,
    'store_id' => $_GET['store'] ?? null,
    'search' => $_GET['search'] ?? null
];

$coupons = $couponModel->getAll($page, ADMIN_ITEMS_PER_PAGE, array_filter($filters));
$totalCoupons = $couponModel->getCount(array_filter($filters));
$pagination = paginate($totalCoupons, ADMIN_ITEMS_PER_PAGE, $page, '/admin/coupons/');

$stores = $storeModel->getActive();

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Coupons</h1>
            <p class="text-gray-500">Manage all coupons and promo codes</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/coupons/add.php" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Coupon
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" 
                       placeholder="Search coupons..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="active" <?php echo ($filters['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="expired" <?php echo ($filters['status'] ?? '') === 'expired' ? 'selected' : ''; ?>>Expired</option>
                <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
            </select>
            <select name="store" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Stores</option>
                <?php foreach ($stores as $store): ?>
                <option value="<?php echo $store['id']; ?>" <?php echo ($filters['store_id'] ?? '') == $store['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($store['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="<?php echo ADMIN_URL; ?>/coupons/" class="px-4 py-2 text-gray-500 hover:text-gray-700">Reset</a>
        </form>
    </div>
    
    <!-- Coupons Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coupon</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($coupons)): ?>
                        <?php foreach ($coupons as $coupon): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="font-medium text-gray-900 max-w-xs truncate"><?php echo htmlspecialchars($coupon['title']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo number_format($coupon['views_count']); ?> views</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <?php if (!empty($coupon['store_logo'])): ?>
                                    <img src="<?php echo UPLOAD_URL . '/' . $coupon['store_logo']; ?>" alt="" class="w-8 h-8 rounded object-cover mr-2">
                                    <?php endif; ?>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($coupon['store_name'] ?? 'N/A'); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($coupon['code']): ?>
                                <span class="px-2 py-1 bg-gray-100 rounded font-mono text-sm"><?php echo htmlspecialchars($coupon['code']); ?></span>
                                <?php else: ?>
                                <span class="text-gray-400 text-sm">No Code</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php echo formatDiscount($coupon['discount_type'], $coupon['discount_value']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($coupon['expiry_date']): ?>
                                <?php echo formatDate($coupon['expiry_date']); ?>
                                <?php else: ?>
                                <span class="text-gray-400">No Expiry</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?php echo $coupon['status'] === 'active' ? 'bg-green-100 text-green-600' : 
                                               ($coupon['status'] === 'expired' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600'); ?>">
                                    <?php echo ucfirst($coupon['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="<?php echo BASE_URL; ?>/coupon/<?php echo $coupon['slug']; ?>" target="_blank" class="p-2 text-gray-500 hover:text-blue-600" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo ADMIN_URL; ?>/coupons/edit.php?id=<?php echo $coupon['id']; ?>" class="p-2 text-gray-500 hover:text-green-600" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo ADMIN_URL; ?>/coupons/?delete=<?php echo $coupon['id']; ?>&token=<?php echo generateCSRFToken(); ?>" 
                                       class="p-2 text-gray-500 hover:text-red-600" title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this coupon?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-ticket-alt text-4xl mb-4 opacity-50"></i>
                                <p>No coupons found</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 border-t">
            <?php echo paginationHtml($pagination, ADMIN_URL . '/coupons/'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
