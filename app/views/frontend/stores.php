<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Page Header -->
<section class="gradient-bg py-12">
    <div class="container mx-auto px-4 text-center text-white">
        <h1 class="text-3xl font-bold mb-2"><?php echo __('all_stores'); ?></h1>
        <p class="text-gray-100">Browse all stores and find the best deals</p>
    </div>
</section>

<!-- Filters -->
<section class="bg-white dark:bg-gray-800 shadow py-4">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm text-gray-500">Filter by:</span>
            <a href="<?php echo BASE_URL; ?>/stores" 
               class="px-4 py-2 rounded-full text-sm <?php echo !$selectedCategory ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'; ?> transition">
                All
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo BASE_URL; ?>/stores?category=<?php echo $cat['id']; ?>" 
               class="px-4 py-2 rounded-full text-sm <?php echo $selectedCategory == $cat['id'] ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'; ?> transition">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Stores Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <?php if (!empty($stores)): ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php foreach ($stores as $store): ?>
            <a href="<?php echo BASE_URL; ?>/store/<?php echo $store['slug']; ?>" 
               class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition text-center group">
                <div class="w-20 h-20 mx-auto mb-4 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden group-hover:scale-105 transition">
                    <?php if ($store['logo']): ?>
                    <img src="<?php echo UPLOAD_URL . '/' . $store['logo']; ?>" alt="<?php echo htmlspecialchars($store['name']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                    <span class="text-3xl font-bold text-primary"><?php echo strtoupper(substr($store['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="font-semibold mb-1 truncate group-hover:text-primary transition"><?php echo htmlspecialchars($store['name']); ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo $store['coupon_count']; ?> <?php echo __('coupons'); ?></p>
                <?php if (!empty($store['category_name'])): ?>
                <span class="inline-block mt-2 px-2 py-1 bg-gray-100 dark:bg-gray-700 text-xs rounded"><?php echo htmlspecialchars($store['category_name']); ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php echo paginationHtml($pagination, '/stores' . ($selectedCategory ? '?category=' . $selectedCategory : '')); ?>
        <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
            <i class="fas fa-store text-5xl text-gray-400 mb-4"></i>
            <p class="text-xl text-gray-500"><?php echo __('no_stores_found'); ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
