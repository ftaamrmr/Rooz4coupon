<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<!-- Store Header -->
<section class="gradient-bg py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center gap-6 text-white">
            <img src="<?= getStoreLogo($store['logo'], $store['name']) ?>" 
                 alt="<?= e($store['name']) ?>" 
                 class="w-24 h-24 rounded-xl object-cover bg-white shadow-lg">
            <div class="text-center md:text-left">
                <?= breadcrumbs([
                    __('Home', 'الرئيسية') => url('/'), 
                    __('Stores', 'المتاجر') => url('stores'),
                    $store['name'] => ''
                ]) ?>
                <h1 class="text-3xl font-bold mb-2"><?= e($store['name']) ?></h1>
                <p class="opacity-90 max-w-2xl"><?= e(getLocalizedField($store, 'description')) ?></p>
                <?php if ($store['website_url']): ?>
                <a href="<?= e($store['website_url']) ?>" target="_blank" class="inline-block mt-3 text-sm opacity-75 hover:opacity-100">
                    <i class="fas fa-external-link-alt mr-1"></i> <?= __('Visit Store', 'زيارة المتجر') ?>
                </a>
                <?php endif; ?>
            </div>
            <div class="md:ml-auto text-center">
                <div class="text-4xl font-bold"><?= count($activeCoupons) ?></div>
                <div class="text-sm opacity-75"><?= __('Active Coupons', 'كوبونات نشطة') ?></div>
            </div>
        </div>
    </div>
</section>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <?php if (empty($activeCoupons)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                <i class="fas fa-ticket-alt text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400">
                    <?= __('No active coupons available', 'لا توجد كوبونات نشطة متاحة') ?>
                </h3>
            </div>
            <?php else: ?>
            
            <h2 class="text-xl font-bold mb-6"><?= __('Available Coupons', 'الكوبونات المتاحة') ?></h2>
            
            <div class="space-y-4">
                <?php foreach ($activeCoupons as $coupon): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="flex flex-col md:flex-row">
                        <!-- Discount Badge -->
                        <div class="md:w-32 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center p-6 text-white">
                            <div class="text-center">
                                <div class="text-2xl font-bold"><?= formatDiscount($coupon) ?></div>
                                <div class="text-xs opacity-75"><?= __('OFF', 'خصم') ?></div>
                            </div>
                        </div>
                        
                        <!-- Coupon Info -->
                        <div class="flex-1 p-6">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <?php if ($coupon['is_verified']): ?>
                                <span class="text-xs bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded">
                                    <i class="fas fa-check-circle mr-1"></i><?= __('Verified', 'موثق') ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($coupon['is_exclusive']): ?>
                                <span class="text-xs bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200 px-2 py-1 rounded">
                                    <i class="fas fa-gem mr-1"></i><?= __('Exclusive', 'حصري') ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <?= e(getLocalizedField($coupon, 'title')) ?>
                            </h3>
                            
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                <?= e(getLocalizedField($coupon, 'description')) ?>
                            </p>
                            
                            <div class="flex flex-wrap items-center gap-4">
                                <?php if (!empty($coupon['code'])): ?>
                                <div class="flex items-center gap-2">
                                    <div class="coupon-code bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded font-mono">
                                        <?= e($coupon['code']) ?>
                                    </div>
                                    <button onclick="copyCode('<?= e($coupon['code']) ?>', this)" 
                                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                                        <i class="fas fa-copy mr-1"></i> <?= __('Copy Code', 'نسخ الكود') ?>
                                    </button>
                                </div>
                                <?php else: ?>
                                <a href="<?= e($coupon['affiliate_url'] ?: $store['website_url'] ?: '#') ?>" 
                                   target="_blank"
                                   class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                                    <?= __('Get Deal', 'احصل على العرض') ?> <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($coupon['expiry_date']): ?>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <i class="far fa-clock mr-1"></i>
                                    <span data-countdown="<?= e($coupon['expiry_date']) ?>"><?= generateCountdown($coupon['expiry_date']) ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="text-sm text-gray-500 dark:text-gray-400 ml-auto">
                                    <i class="fas fa-users mr-1"></i> <?= $coupon['uses_count'] ?> <?= __('uses', 'استخدام') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php endif; ?>
            
            <!-- Expired Coupons -->
            <?php if (!empty($expiredCoupons)): ?>
            <div class="mt-12">
                <h2 class="text-xl font-bold mb-6 text-gray-500"><?= __('Expired Coupons', 'كوبونات منتهية') ?></h2>
                <div class="space-y-4 opacity-60">
                    <?php foreach ($expiredCoupons as $coupon): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 flex items-center gap-4">
                        <span class="text-red-500"><i class="fas fa-times-circle"></i></span>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-600 dark:text-gray-400 line-through">
                                <?= e(getLocalizedField($coupon, 'title')) ?>
                            </h4>
                        </div>
                        <span class="text-sm text-gray-500"><?= __('Expired', 'منتهي') ?> <?= formatDate($coupon['expiry_date']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-80 flex-shrink-0 space-y-6">
            <!-- Store Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                <h3 class="font-semibold mb-4"><?= __('About', 'حول') ?> <?= e($store['name']) ?></h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    <?= e(getLocalizedField($store, 'description')) ?>
                </p>
                <?php if ($store['category_name']): ?>
                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                    <span class="text-sm text-gray-500"><?= __('Category', 'الفئة') ?>:</span>
                    <a href="<?= url('category/' . $store['category_slug']) ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">
                        <?= e($store['category_name']) ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Related Stores -->
            <?php if (!empty($relatedStores)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                <h3 class="font-semibold mb-4"><?= __('Related Stores', 'متاجر مشابهة') ?></h3>
                <div class="space-y-3">
                    <?php foreach ($relatedStores as $related): ?>
                    <a href="<?= url('store/' . $related['slug']) ?>" 
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        <img src="<?= getStoreLogo($related['logo'], $related['name']) ?>" 
                             alt="<?= e($related['name']) ?>" 
                             class="w-10 h-10 rounded-lg object-cover bg-gray-100">
                        <span class="font-medium"><?= e($related['name']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Ad Placeholder -->
            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-8 text-center">
                <p class="text-gray-400 text-sm"><?= __('Advertisement', 'إعلان') ?></p>
            </div>
        </aside>
    </div>
</div>

<!-- Schema.org markup for Store -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Store",
    "name": <?= json_encode($store['name']) ?>,
    "description": <?= json_encode(getLocalizedField($store, 'description')) ?>,
    "url": <?= json_encode($store['website_url'] ?: url('store/' . $store['slug'])) ?>
    <?php if ($store['logo']): ?>
    ,"image": <?= json_encode(upload($store['logo'])) ?>
    <?php endif; ?>
}
</script>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
