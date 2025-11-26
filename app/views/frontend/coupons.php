<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([__('Home', 'الرئيسية') => url('/'), __('Coupons', 'كوبونات') => '']) ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md sticky top-24">
                <h3 class="font-semibold text-lg mb-4"><?= __('Filter by Type', 'تصفية حسب النوع') ?></h3>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= url('coupons') ?>" 
                           class="block py-2 px-3 rounded-lg <?= !$selectedType ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <?= __('All Types', 'جميع الأنواع') ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('coupons?type=percentage') ?>" 
                           class="block py-2 px-3 rounded-lg <?= $selectedType === 'percentage' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <i class="fas fa-percent mr-2"></i><?= __('Percentage Off', 'نسبة خصم') ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('coupons?type=fixed') ?>" 
                           class="block py-2 px-3 rounded-lg <?= $selectedType === 'fixed' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <i class="fas fa-dollar-sign mr-2"></i><?= __('Fixed Amount', 'مبلغ ثابت') ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('coupons?type=freebie') ?>" 
                           class="block py-2 px-3 rounded-lg <?= $selectedType === 'freebie' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <i class="fas fa-gift mr-2"></i><?= __('Free Gifts', 'هدايا مجانية') ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('coupons?type=deal') ?>" 
                           class="block py-2 px-3 rounded-lg <?= $selectedType === 'deal' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <i class="fas fa-tag mr-2"></i><?= __('Deals', 'عروض') ?>
                        </a>
                    </li>
                </ul>
                
                <div class="mt-6 pt-6 border-t dark:border-gray-700">
                    <a href="<?= url('expired-coupons') ?>" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-sm">
                        <i class="fas fa-history mr-1"></i> <?= __('View Expired Coupons', 'عرض الكوبونات المنتهية') ?>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h1 class="text-2xl font-bold"><?= __('All Coupons', 'جميع الكوبونات') ?> <span class="text-gray-500 font-normal">(<?= $totalCoupons ?>)</span></h1>
            </div>
            
            <?php if (empty($coupons)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                <i class="fas fa-ticket-alt text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400"><?= __('No coupons found', 'لا توجد كوبونات') ?></h3>
            </div>
            <?php else: ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($coupons as $coupon): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="<?= getStoreLogo($coupon['store_logo'], $coupon['store_name']) ?>" 
                                 alt="<?= e($coupon['store_name']) ?>" 
                                 class="w-12 h-12 rounded-lg object-cover bg-gray-100">
                            <div class="flex-1 min-w-0">
                                <a href="<?= url('store/' . $coupon['store_slug']) ?>" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <?= e($coupon['store_name']) ?>
                                </a>
                                <div class="flex items-center gap-2">
                                    <?php if ($coupon['is_verified']): ?>
                                    <span class="text-xs text-green-600 dark:text-green-400"><i class="fas fa-check-circle"></i></span>
                                    <?php endif; ?>
                                    <?php if ($coupon['is_exclusive']): ?>
                                    <span class="text-xs text-purple-600 dark:text-purple-400"><i class="fas fa-gem"></i></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-200 px-2 py-1 rounded text-xs font-bold">
                                <?= formatDiscount($coupon) ?>
                            </span>
                        </div>
                        
                        <h3 class="font-medium text-gray-900 dark:text-white line-clamp-2 min-h-[48px]">
                            <?= e(getLocalizedField($coupon, 'title')) ?>
                        </h3>
                        
                        <div class="flex items-center justify-between mt-4">
                            <?php if (!empty($coupon['code'])): ?>
                            <button onclick="copyCode('<?= e($coupon['code']) ?>', this)" 
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-copy mr-1"></i> <?= __('Copy Code', 'نسخ الكود') ?>
                            </button>
                            <?php else: ?>
                            <a href="<?= e($coupon['affiliate_url'] ?: url('coupon/' . $coupon['id'])) ?>" 
                               class="flex-1 text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                                <?= __('Get Deal', 'احصل على العرض') ?>
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($coupon['expiry_date']): ?>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                            <i class="far fa-clock mr-1"></i> <?= generateCountdown($coupon['expiry_date']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?= paginate($currentPage, $totalPages, url('coupons')) ?>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
