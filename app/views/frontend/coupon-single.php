<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([
        __('Home', 'الرئيسية') => url('/'), 
        __('Coupons', 'الكوبونات') => url('coupons'),
        getLocalizedField($coupon, 'title') => ''
    ]) ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Discount Badge -->
                    <div class="md:w-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center p-8 text-white">
                        <div class="text-center">
                            <div class="text-4xl font-bold"><?= formatDiscount($coupon) ?></div>
                            <div class="text-sm opacity-75 mt-1"><?= __('OFF', 'خصم') ?></div>
                        </div>
                    </div>
                    
                    <!-- Coupon Info -->
                    <div class="flex-1 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <a href="<?= url('store/' . $coupon['store_slug']) ?>">
                                <img src="<?= getStoreLogo($coupon['store_logo'], $coupon['store_name']) ?>" 
                                     alt="<?= e($coupon['store_name']) ?>" 
                                     class="w-16 h-16 rounded-lg object-cover bg-gray-100">
                            </a>
                            <div>
                                <a href="<?= url('store/' . $coupon['store_slug']) ?>" 
                                   class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <?= e($coupon['store_name']) ?>
                                </a>
                                <div class="flex flex-wrap gap-2 mt-1">
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
                            </div>
                        </div>
                        
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <?= e(getLocalizedField($coupon, 'title')) ?>
                        </h1>
                        
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            <?= e(getLocalizedField($coupon, 'description')) ?>
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-4">
                            <?php if (!empty($coupon['code'])): ?>
                            <div class="flex items-center gap-3">
                                <div class="coupon-code bg-gray-100 dark:bg-gray-700 px-6 py-3 rounded-lg font-mono text-lg">
                                    <?= e($coupon['code']) ?>
                                </div>
                                <button onclick="copyCode('<?= e($coupon['code']) ?>', this)" 
                                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-lg">
                                    <i class="fas fa-copy mr-2"></i> <?= __('Copy Code', 'نسخ الكود') ?>
                                </button>
                            </div>
                            <?php else: ?>
                            <?php 
                            $dealUrl = $coupon['affiliate_url'] ?: ($coupon['store_url'] ?: url('store/' . $coupon['store_slug']));
                            ?>
                            <a href="<?= e($dealUrl) ?>" 
                               target="_blank"
                               class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-lg">
                                <?= __('Get Deal', 'احصل على العرض') ?> <i class="fas fa-external-link-alt ml-2"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-6 mt-6 pt-6 border-t dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                            <?php if ($coupon['expiry_date']): ?>
                            <div>
                                <i class="far fa-clock mr-1"></i>
                                <span data-countdown="<?= e($coupon['expiry_date']) ?>"><?= generateCountdown($coupon['expiry_date']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div>
                                <i class="fas fa-eye mr-1"></i> <?= $coupon['views_count'] ?> <?= __('views', 'مشاهدة') ?>
                            </div>
                            <div>
                                <i class="fas fa-users mr-1"></i> <?= $coupon['uses_count'] ?> <?= __('uses', 'استخدام') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Coupons -->
            <?php if (!empty($relatedCoupons)): ?>
            <div class="mt-10">
                <h2 class="text-xl font-bold mb-6"><?= __('More Coupons from', 'المزيد من الكوبونات من') ?> <?= e($coupon['store_name']) ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($relatedCoupons as $related): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">
                                    <?= e(getLocalizedField($related, 'title')) ?>
                                </h3>
                                <?php if ($related['expiry_date']): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="far fa-clock mr-1"></i> <?= generateCountdown($related['expiry_date']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <span class="bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-200 px-3 py-1 rounded-full text-sm font-bold">
                                <?= formatDiscount($related) ?>
                            </span>
                        </div>
                        <div class="mt-4">
                            <?php if (!empty($related['code'])): ?>
                            <button onclick="copyCode('<?= e($related['code']) ?>', this)" 
                                    class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-copy mr-1"></i> <?= __('Copy Code', 'نسخ الكود') ?>
                            </button>
                            <?php else: ?>
                            <a href="<?= url('coupon/' . $related['id']) ?>" 
                               class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                                <?= __('View Deal', 'عرض العرض') ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-80 flex-shrink-0 space-y-6">
            <!-- Store Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md text-center">
                <a href="<?= url('store/' . $coupon['store_slug']) ?>">
                    <img src="<?= getStoreLogo($coupon['store_logo'], $coupon['store_name']) ?>" 
                         alt="<?= e($coupon['store_name']) ?>" 
                         class="w-20 h-20 mx-auto rounded-xl object-cover bg-gray-100 mb-4">
                </a>
                <h3 class="font-semibold text-lg"><?= e($coupon['store_name']) ?></h3>
                <p class="text-gray-500 text-sm mt-2 line-clamp-3">
                    <?= e($coupon['store_description']) ?>
                </p>
                <a href="<?= url('store/' . $coupon['store_slug']) ?>" 
                   class="inline-block mt-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                    <?= __('View All Coupons', 'عرض جميع الكوبونات') ?>
                </a>
            </div>
            
            <!-- Ad Placeholder -->
            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-8 text-center">
                <p class="text-gray-400 text-sm"><?= __('Advertisement', 'إعلان') ?></p>
            </div>
        </aside>
    </div>
</div>

<!-- Schema.org markup for Coupon -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Offer",
    "name": <?= json_encode(getLocalizedField($coupon, 'title')) ?>,
    "description": <?= json_encode(getLocalizedField($coupon, 'description')) ?>,
    "seller": {
        "@type": "Organization",
        "name": <?= json_encode($coupon['store_name']) ?>
    }
    <?php if ($coupon['expiry_date']): ?>
    ,"validThrough": <?= json_encode($coupon['expiry_date'] . 'T23:59:59') ?>
    <?php endif; ?>
}
</script>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
