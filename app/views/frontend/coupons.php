<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Page Header -->
<section class="gradient-bg py-12">
    <div class="container mx-auto px-4 text-center text-white">
        <h1 class="text-3xl font-bold mb-2"><?php echo __('all_coupons'); ?></h1>
        <p class="text-gray-100">Browse all available coupons and promo codes</p>
    </div>
</section>

<!-- Coupons Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-2/3">
                <?php if (!empty($coupons)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($coupons as $coupon): ?>
                    <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php echo paginationHtml($pagination, '/coupons'); ?>
                <?php else: ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                    <i class="fas fa-ticket-alt text-5xl text-gray-400 mb-4"></i>
                    <p class="text-xl text-gray-500"><?php echo __('no_coupons_found'); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="lg:w-1/3">
                <!-- Categories -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow mb-6">
                    <h3 class="font-bold mb-4"><?php echo __('browse_by_category'); ?></h3>
                    <div class="space-y-2">
                        <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo BASE_URL; ?>/category/<?php echo $cat['slug']; ?>" 
                           class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="flex items-center">
                                <?php if ($cat['icon']): ?>
                                <i class="fas <?php echo htmlspecialchars($cat['icon']); ?> w-6 text-primary"></i>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            </span>
                            <span class="text-sm text-gray-500"><?php echo $cat['coupon_count']; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Ad Slot -->
                <?php if (getSetting('ad_sidebar')): ?>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4">
                    <?php echo getSetting('ad_sidebar'); ?>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
