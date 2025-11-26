<?php
/**
 * Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';
require_once APP_PATH . '/models/Coupon.php';
require_once APP_PATH . '/models/Store.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Article.php';
require_once APP_PATH . '/models/User.php';

requireAuth();

// Get statistics
$couponModel = new Coupon();
$storeModel = new Store();
$categoryModel = new Category();
$articleModel = new Article();
$userModel = new User();

$couponStats = $couponModel->getStats();
$storeStats = $storeModel->getStats();
$categoryStats = $categoryModel->getStats();
$articleStats = $articleModel->getStats();
$userStats = $userModel->getStats();

// Get latest coupons
$latestCoupons = $couponModel->getAll(1, 5);
$latestArticles = $articleModel->getAll(1, 5);

include __DIR__ . '/includes/header.php';
?>

<div class="p-6">
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!</h1>
        <p class="text-gray-500 mt-1">Here's what's happening with your coupon website today.</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Coupons -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Coupons</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($couponStats['total'] ?? 0); ?></p>
                    <p class="text-sm mt-2">
                        <span class="text-green-500"><?php echo number_format($couponStats['active'] ?? 0); ?> active</span>
                        <span class="text-gray-400 mx-1">•</span>
                        <span class="text-red-500"><?php echo number_format($couponStats['expired'] ?? 0); ?> expired</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-2xl text-blue-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Stores -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Stores</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($storeStats['total'] ?? 0); ?></p>
                    <p class="text-sm mt-2">
                        <span class="text-green-500"><?php echo number_format($storeStats['active'] ?? 0); ?> active</span>
                        <span class="text-gray-400 mx-1">•</span>
                        <span class="text-purple-500"><?php echo number_format($storeStats['featured'] ?? 0); ?> featured</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-store text-2xl text-green-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Articles -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Articles</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($articleStats['total'] ?? 0); ?></p>
                    <p class="text-sm mt-2">
                        <span class="text-green-500"><?php echo number_format($articleStats['published'] ?? 0); ?> published</span>
                        <span class="text-gray-400 mx-1">•</span>
                        <span class="text-yellow-500"><?php echo number_format($articleStats['draft'] ?? 0); ?> draft</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-newspaper text-2xl text-purple-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Views -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Views</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format(($couponStats['total_views'] ?? 0) + ($storeStats['total_views'] ?? 0)); ?></p>
                    <p class="text-sm mt-2">
                        <span class="text-blue-500"><?php echo number_format($couponStats['total_used'] ?? 0); ?> codes used</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-eye text-2xl text-orange-500"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="<?php echo ADMIN_URL; ?>/coupons/add.php" class="bg-blue-500 hover:bg-blue-600 text-white rounded-xl p-4 text-center transition">
            <i class="fas fa-plus text-2xl mb-2"></i>
            <p class="font-medium">Add Coupon</p>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/stores/add.php" class="bg-green-500 hover:bg-green-600 text-white rounded-xl p-4 text-center transition">
            <i class="fas fa-store text-2xl mb-2"></i>
            <p class="font-medium">Add Store</p>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/articles/add.php" class="bg-purple-500 hover:bg-purple-600 text-white rounded-xl p-4 text-center transition">
            <i class="fas fa-pen text-2xl mb-2"></i>
            <p class="font-medium">Write Article</p>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/settings/appearance.php" class="bg-orange-500 hover:bg-orange-600 text-white rounded-xl p-4 text-center transition">
            <i class="fas fa-paint-brush text-2xl mb-2"></i>
            <p class="font-medium">Customize</p>
        </a>
    </div>
    
    <!-- Recent Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Latest Coupons -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-lg font-bold text-gray-800">Latest Coupons</h2>
                <a href="<?php echo ADMIN_URL; ?>/coupons/" class="text-blue-500 hover:underline text-sm">View All</a>
            </div>
            <div class="divide-y">
                <?php if (!empty($latestCoupons)): ?>
                    <?php foreach ($latestCoupons as $coupon): ?>
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <?php if (!empty($coupon['store_logo'])): ?>
                                    <img src="<?php echo UPLOAD_URL . '/' . $coupon['store_logo']; ?>" alt="" class="w-full h-full rounded-lg object-cover">
                                    <?php else: ?>
                                    <i class="fas fa-ticket-alt text-gray-400"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 truncate max-w-xs"><?php echo htmlspecialchars($coupon['title']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($coupon['store_name'] ?? 'No Store'); ?></p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full 
                                <?php echo $coupon['status'] === 'active' ? 'bg-green-100 text-green-600' : 
                                           ($coupon['status'] === 'expired' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600'); ?>">
                                <?php echo ucfirst($coupon['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-ticket-alt text-4xl mb-2 opacity-50"></i>
                        <p>No coupons yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Latest Articles -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-lg font-bold text-gray-800">Latest Articles</h2>
                <a href="<?php echo ADMIN_URL; ?>/articles/" class="text-blue-500 hover:underline text-sm">View All</a>
            </div>
            <div class="divide-y">
                <?php if (!empty($latestArticles)): ?>
                    <?php foreach ($latestArticles as $article): ?>
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                    <?php if (!empty($article['cover_image'])): ?>
                                    <img src="<?php echo UPLOAD_URL . '/' . $article['cover_image']; ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <i class="fas fa-newspaper text-gray-400"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 truncate max-w-xs"><?php echo htmlspecialchars($article['title']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo formatDate($article['publish_date'] ?? $article['created_at']); ?></p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full 
                                <?php echo $article['status'] === 'published' ? 'bg-green-100 text-green-600' : 
                                           ($article['status'] === 'draft' ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600'); ?>">
                                <?php echo ucfirst($article['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-newspaper text-4xl mb-2 opacity-50"></i>
                        <p>No articles yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
