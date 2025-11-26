<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([__('Home', 'الرئيسية') => url('/'), __('Expired Coupons', 'كوبونات منتهية') => '']) ?>
    
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-500"><?= __('Expired Coupons Archive', 'أرشيف الكوبونات المنتهية') ?></h1>
            <a href="<?= url('coupons') ?>" class="text-indigo-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> <?= __('Active Coupons', 'الكوبونات النشطة') ?>
            </a>
        </div>
        
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-900 rounded-xl p-4 mb-6">
            <p class="text-yellow-700 dark:text-yellow-200 text-sm">
                <i class="fas fa-info-circle mr-1"></i>
                <?= __('These coupons have expired and may no longer work. Check our active coupons for current deals.', 'هذه الكوبونات منتهية الصلاحية وقد لا تعمل. تحقق من الكوبونات النشطة للحصول على العروض الحالية.') ?>
            </p>
        </div>
        
        <?php if (empty($coupons)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
            <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400"><?= __('No expired coupons', 'لا توجد كوبونات منتهية') ?></h3>
        </div>
        <?php else: ?>
        
        <div class="space-y-4 opacity-75">
            <?php foreach ($coupons as $coupon): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow flex items-center gap-4">
                <img src="<?= getStoreLogo($coupon['store_logo'], $coupon['store_name']) ?>" 
                     alt="<?= e($coupon['store_name']) ?>" 
                     class="w-12 h-12 rounded-lg object-cover bg-gray-100 grayscale">
                <div class="flex-1 min-w-0">
                    <a href="<?= url('store/' . $coupon['store_slug']) ?>" class="text-sm text-gray-500">
                        <?= e($coupon['store_name']) ?>
                    </a>
                    <h3 class="font-medium text-gray-600 dark:text-gray-400 line-through">
                        <?= e(getLocalizedField($coupon, 'title')) ?>
                    </h3>
                </div>
                <div class="text-right">
                    <span class="text-gray-400 text-sm"><?= formatDiscount($coupon) ?></span>
                    <div class="text-xs text-red-500">
                        <?= __('Expired', 'منتهي') ?> <?= formatDate($coupon['expiry_date']) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?= paginate($currentPage, $totalPages, url('expired-coupons')) ?>
        
        <?php endif; ?>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
