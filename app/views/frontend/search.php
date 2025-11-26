<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Page Header -->
<section class="gradient-bg py-12">
    <div class="container mx-auto px-4 text-center text-white">
        <h1 class="text-3xl font-bold mb-2"><?php echo __('search_results_for'); ?> "<?php echo htmlspecialchars($query); ?>"</h1>
    </div>
</section>

<!-- Search Results -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <?php if (empty($query)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
            <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
            <p class="text-xl text-gray-500">Please enter a search term</p>
        </div>
        <?php elseif (empty($coupons) && empty($stores) && empty($articles)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
            <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
            <p class="text-xl text-gray-500 mb-4"><?php echo __('no_results'); ?></p>
            <p class="text-gray-400">Try searching for something else</p>
        </div>
        <?php else: ?>
        
        <!-- Stores Results -->
        <?php if (!empty($stores)): ?>
        <div class="mb-12">
            <h2 class="text-xl font-bold mb-6"><?php echo __('stores'); ?> (<?php echo count($stores); ?>)</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($stores as $store): ?>
                <a href="<?php echo BASE_URL; ?>/store/<?php echo $store['slug']; ?>" 
                   class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 mx-auto mb-2 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                        <?php if ($store['logo']): ?>
                        <img src="<?php echo UPLOAD_URL . '/' . $store['logo']; ?>" alt="" class="w-full h-full object-cover">
                        <?php else: ?>
                        <span class="text-xl font-bold text-primary"><?php echo strtoupper(substr($store['name'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-medium text-sm truncate"><?php echo htmlspecialchars($store['name']); ?></h3>
                    <p class="text-xs text-gray-500"><?php echo $store['coupon_count']; ?> coupons</p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Coupons Results -->
        <?php if (!empty($coupons)): ?>
        <div class="mb-12">
            <h2 class="text-xl font-bold mb-6"><?php echo __('coupons'); ?> (<?php echo count($coupons); ?>)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($coupons as $coupon): ?>
                <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Articles Results -->
        <?php if (!empty($articles)): ?>
        <div>
            <h2 class="text-xl font-bold mb-6">Articles (<?php echo count($articles); ?>)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($articles as $article): ?>
                <article class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                    <?php if ($article['cover_image']): ?>
                    <a href="<?php echo BASE_URL; ?>/blog/<?php echo $article['slug']; ?>">
                        <img src="<?php echo UPLOAD_URL . '/' . $article['cover_image']; ?>" alt="" class="w-full h-48 object-cover">
                    </a>
                    <?php endif; ?>
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-2"><?php echo formatDate($article['publish_date']); ?></div>
                        <h3 class="font-semibold mb-2 line-clamp-2">
                            <a href="<?php echo BASE_URL; ?>/blog/<?php echo $article['slug']; ?>" class="hover:text-primary transition">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </a>
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                            <?php echo htmlspecialchars($article['excerpt'] ?? truncate(strip_tags($article['content']), 100)); ?>
                        </p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
