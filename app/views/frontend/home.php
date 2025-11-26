<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Hero Section -->
<section class="gradient-bg py-16 md:py-24">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">
            <?php echo isRTL() ? getSetting('hero_title_ar', 'اكتشف أفضل العروض') : getSetting('hero_title', 'Find the Best Deals'); ?>
        </h1>
        <p class="text-xl text-gray-100 mb-8 max-w-2xl mx-auto">
            <?php echo isRTL() ? getSetting('hero_subtitle_ar', 'وفر المال مع كوبونات وخصومات حصرية') : getSetting('hero_subtitle', 'Save money with exclusive coupons and discounts from top stores'); ?>
        </p>
        
        <!-- Search Bar -->
        <div class="max-w-xl mx-auto">
            <form action="<?php echo BASE_URL; ?>/search" method="GET" class="relative">
                <input type="text" name="q" placeholder="<?php echo __('search_placeholder'); ?>"
                       class="w-full px-6 py-4 rounded-full text-lg bg-white shadow-lg focus:outline-none focus:ring-4 focus:ring-white/30">
                <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 px-6 py-2 bg-primary text-white rounded-full hover:bg-secondary transition">
                    <i class="fas fa-search mr-2"></i><?php echo __('search'); ?>
                </button>
            </form>
        </div>
        
        <!-- Quick Stats -->
        <div class="flex flex-wrap justify-center gap-8 mt-12">
            <?php
            $couponModel = new Coupon();
            $storeModel = new Store();
            $stats = $couponModel->getStats();
            $storeStats = $storeModel->getStats();
            ?>
            <div class="text-white">
                <div class="text-3xl font-bold"><?php echo number_format($stats['active'] ?? 0); ?>+</div>
                <div class="text-gray-200">Active Coupons</div>
            </div>
            <div class="text-white">
                <div class="text-3xl font-bold"><?php echo number_format($storeStats['total'] ?? 0); ?>+</div>
                <div class="text-gray-200">Stores</div>
            </div>
            <div class="text-white">
                <div class="text-3xl font-bold"><?php echo number_format($stats['total_used'] ?? 0); ?>+</div>
                <div class="text-gray-200">Savings Made</div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Slider -->
<section class="py-8 bg-white dark:bg-gray-800 shadow">
    <div class="container mx-auto px-4">
        <div class="flex overflow-x-auto space-x-4 rtl:space-x-reverse pb-2 scrollbar-hide">
            <?php foreach ($categories as $category): ?>
            <a href="<?php echo BASE_URL; ?>/category/<?php echo $category['slug']; ?>" 
               class="flex-shrink-0 flex items-center space-x-2 rtl:space-x-reverse px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-full hover:bg-primary hover:text-white transition">
                <?php if ($category['icon']): ?>
                <i class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i>
                <?php endif; ?>
                <span class="whitespace-nowrap"><?php echo isRTL() && $category['name_ar'] ? htmlspecialchars($category['name_ar']) : htmlspecialchars($category['name']); ?></span>
                <span class="text-xs bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded-full"><?php echo $category['coupon_count']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Stores -->
<?php if (!empty($featuredStores)): ?>
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold"><?php echo __('featured'); ?> <?php echo __('stores'); ?></h2>
            <a href="<?php echo BASE_URL; ?>/stores" class="text-primary hover:underline"><?php echo __('all_stores'); ?> →</a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach ($featuredStores as $store): ?>
            <a href="<?php echo BASE_URL; ?>/store/<?php echo $store['slug']; ?>" 
               class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow hover:shadow-lg transition text-center group">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                    <?php if ($store['logo']): ?>
                    <img src="<?php echo UPLOAD_URL . '/' . $store['logo']; ?>" alt="<?php echo htmlspecialchars($store['name']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                    <span class="text-2xl font-bold text-primary"><?php echo strtoupper(substr($store['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="font-semibold group-hover:text-primary transition truncate"><?php echo htmlspecialchars($store['name']); ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo $store['coupon_count']; ?> <?php echo __('coupons'); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Coupons -->
<?php if (!empty($featuredCoupons)): ?>
<section class="py-12 bg-gray-100 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold"><?php echo __('featured'); ?> <?php echo __('coupons'); ?></h2>
            <a href="<?php echo BASE_URL; ?>/coupons" class="text-primary hover:underline"><?php echo __('all_coupons'); ?> →</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($featuredCoupons as $coupon): ?>
            <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Coupons -->
<?php if (!empty($latestCoupons)): ?>
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold"><?php echo __('latest'); ?> <?php echo __('coupons'); ?></h2>
            <a href="<?php echo BASE_URL; ?>/coupons" class="text-primary hover:underline"><?php echo __('view_all_coupons'); ?> →</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($latestCoupons as $coupon): ?>
            <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Coupons -->
<?php if (!empty($popularCoupons)): ?>
<section class="py-12 bg-gray-100 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold"><?php echo __('popular'); ?> <?php echo __('coupons'); ?></h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($popularCoupons as $coupon): ?>
            <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Articles -->
<?php if (!empty($latestArticles)): ?>
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold"><?php echo __('latest'); ?> Articles</h2>
            <a href="<?php echo BASE_URL; ?>/blog" class="text-primary hover:underline">View All →</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($latestArticles as $article): ?>
            <article class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                <?php if ($article['cover_image']): ?>
                <a href="<?php echo BASE_URL; ?>/blog/<?php echo $article['slug']; ?>">
                    <img src="<?php echo UPLOAD_URL . '/' . $article['cover_image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-48 object-cover">
                </a>
                <?php endif; ?>
                <div class="p-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                        <?php echo formatDate($article['publish_date']); ?>
                    </div>
                    <h3 class="font-semibold mb-2 line-clamp-2">
                        <a href="<?php echo BASE_URL; ?>/blog/<?php echo $article['slug']; ?>" class="hover:text-primary transition">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </a>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2"><?php echo htmlspecialchars($article['excerpt'] ?? truncate(strip_tags($article['content']), 100)); ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
