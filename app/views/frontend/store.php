<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Store Schema.org Markup -->
<?php
echo schemaJsonLd('Store', [
    'name' => $store['name'],
    'description' => $store['description'] ?? '',
    'url' => BASE_URL . '/store/' . $store['slug'],
    'image' => $store['logo'] ? UPLOAD_URL . '/' . $store['logo'] : '',
    'sameAs' => $store['website'] ?? ''
]);
?>

<!-- Breadcrumbs -->
<div class="bg-gray-100 dark:bg-gray-800 py-4">
    <div class="container mx-auto px-4">
        <?php
        echo breadcrumbs([
            ['title' => __('home'), 'url' => BASE_URL],
            ['title' => __('stores'), 'url' => BASE_URL . '/stores'],
            ['title' => $store['name'], 'url' => '']
        ]);
        ?>
    </div>
</div>

<!-- Store Header -->
<section class="bg-white dark:bg-gray-800 shadow">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <!-- Store Logo -->
            <div class="w-32 h-32 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden shadow-lg">
                <?php if ($store['logo']): ?>
                <img src="<?php echo UPLOAD_URL . '/' . $store['logo']; ?>" alt="<?php echo htmlspecialchars($store['name']); ?>" class="w-full h-full object-cover">
                <?php else: ?>
                <span class="text-5xl font-bold text-primary"><?php echo strtoupper(substr($store['name'], 0, 1)); ?></span>
                <?php endif; ?>
            </div>
            
            <!-- Store Info -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($store['name']); ?></h1>
                
                <?php if ($store['category_name']): ?>
                <a href="<?php echo BASE_URL; ?>/category/<?php echo $store['category_slug']; ?>" class="inline-block text-primary hover:underline mb-3">
                    <i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($store['category_name']); ?>
                </a>
                <?php endif; ?>
                
                <?php if ($store['description']): ?>
                <p class="text-gray-600 dark:text-gray-300 mb-4 max-w-2xl">
                    <?php echo htmlspecialchars(isRTL() && $store['description_ar'] ? $store['description_ar'] : $store['description']); ?>
                </p>
                <?php endif; ?>
                
                <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <i class="fas fa-ticket-alt text-primary"></i>
                        <span class="font-semibold"><?php echo $store['coupon_count']; ?></span>
                        <span class="text-gray-500"><?php echo __('active_coupons'); ?></span>
                    </div>
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <i class="fas fa-eye text-primary"></i>
                        <span class="font-semibold"><?php echo number_format($store['views_count']); ?></span>
                        <span class="text-gray-500">Views</span>
                    </div>
                </div>
            </div>
            
            <!-- Visit Store Button -->
            <?php if ($store['website']): ?>
            <div>
                <a href="<?php echo htmlspecialchars($store['affiliate_url'] ?? $store['website']); ?>" target="_blank" rel="nofollow noopener"
                   class="inline-flex items-center px-6 py-3 bg-primary hover:bg-secondary text-white rounded-lg font-semibold transition">
                    <i class="fas fa-external-link-alt mr-2"></i><?php echo __('visit_store'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Coupons List -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-2/3">
                <h2 class="text-2xl font-bold mb-6"><?php echo htmlspecialchars($store['name']); ?> <?php echo __('coupons'); ?></h2>
                
                <?php if (!empty($coupons)): ?>
                <div class="space-y-6">
                    <?php foreach ($coupons as $coupon): ?>
                    <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center">
                    <i class="fas fa-ticket-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500"><?php echo __('no_coupons_found'); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Expired Coupons -->
                <?php if (!empty($expiredCoupons)): ?>
                <div class="mt-12">
                    <h3 class="text-xl font-bold mb-4 text-gray-500"><?php echo __('expired_coupons'); ?></h3>
                    <p class="text-sm text-gray-500 mb-4">These coupons have expired but may still work. Try them at your own risk.</p>
                    
                    <div class="space-y-4 opacity-75">
                        <?php foreach ($expiredCoupons as $coupon): ?>
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium line-through text-gray-500"><?php echo htmlspecialchars($coupon['title']); ?></h4>
                                    <?php if ($coupon['code']): ?>
                                    <span class="text-sm font-mono text-gray-400"><?php echo htmlspecialchars($coupon['code']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-sm text-red-500"><?php echo __('expired_on'); ?> <?php echo formatDate($coupon['expiry_date']); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="lg:w-1/3">
                <!-- Ad Slot -->
                <?php if (getSetting('ad_coupon_sidebar')): ?>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 mb-6">
                    <?php echo getSetting('ad_coupon_sidebar'); ?>
                </div>
                <?php endif; ?>
                
                <!-- Store Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow mb-6">
                    <h3 class="font-bold mb-4"><?php echo __('about_store'); ?></h3>
                    
                    <?php if ($store['website']): ?>
                    <div class="flex items-center space-x-3 rtl:space-x-reverse mb-3">
                        <i class="fas fa-globe text-gray-400 w-5"></i>
                        <a href="<?php echo htmlspecialchars($store['website']); ?>" target="_blank" rel="nofollow noopener" class="text-primary hover:underline truncate">
                            <?php echo htmlspecialchars(parse_url($store['website'], PHP_URL_HOST)); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($store['category_name']): ?>
                    <div class="flex items-center space-x-3 rtl:space-x-reverse mb-3">
                        <i class="fas fa-folder text-gray-400 w-5"></i>
                        <a href="<?php echo BASE_URL; ?>/category/<?php echo $store['category_slug']; ?>" class="text-primary hover:underline">
                            <?php echo htmlspecialchars($store['category_name']); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-ticket-alt text-gray-400 w-5"></i>
                        <span><?php echo $store['coupon_count']; ?> Active Coupons</span>
                    </div>
                </div>
                
                <!-- Related Stores -->
                <?php if (!empty($store['category_id'])): 
                    $storeModel = new Store();
                    $relatedStores = $storeModel->getByCategory($store['category_id'], 5);
                    $relatedStores = array_filter($relatedStores, function($s) use ($store) { return $s['id'] != $store['id']; });
                ?>
                <?php if (!empty($relatedStores)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
                    <h3 class="font-bold mb-4">Related Stores</h3>
                    <div class="space-y-3">
                        <?php foreach (array_slice($relatedStores, 0, 5) as $relStore): ?>
                        <a href="<?php echo BASE_URL; ?>/store/<?php echo $relStore['slug']; ?>" class="flex items-center space-x-3 rtl:space-x-reverse p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-600 flex items-center justify-center overflow-hidden">
                                <?php if ($relStore['logo']): ?>
                                <img src="<?php echo UPLOAD_URL . '/' . $relStore['logo']; ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                <span class="font-bold text-primary"><?php echo strtoupper(substr($relStore['name'], 0, 1)); ?></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="font-medium"><?php echo htmlspecialchars($relStore['name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo $relStore['coupon_count']; ?> coupons</div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
