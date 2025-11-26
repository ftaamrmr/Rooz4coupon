<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([
        __('Home', 'الرئيسية') => url('/'), 
        __('Categories', 'الفئات') => url('categories'),
        getLocalizedField($category, 'name') => ''
    ]) ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-8 mb-8 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas <?= e($category['icon'] ?: 'fa-tag') ?> text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold"><?= e(getLocalizedField($category, 'name')) ?></h1>
                        <p class="opacity-90"><?= e(getLocalizedField($category, 'description')) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Subcategories -->
            <?php if (!empty($subcategories)): ?>
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4"><?= __('Subcategories', 'الفئات الفرعية') ?></h2>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($subcategories as $sub): ?>
                    <a href="<?= url('category/' . $sub['slug']) ?>" 
                       class="px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md">
                        <?= e(getLocalizedField($sub, 'name')) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Stores in Category -->
            <?php if (!empty($stores)): ?>
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4"><?= __('Stores in this Category', 'المتاجر في هذه الفئة') ?></h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <?php foreach ($stores as $store): ?>
                    <a href="<?= url('store/' . $store['slug']) ?>" 
                       class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center shadow hover:shadow-md">
                        <img src="<?= getStoreLogo($store['logo'], $store['name']) ?>" 
                             alt="<?= e($store['name']) ?>" 
                             class="w-12 h-12 mx-auto rounded-lg object-cover bg-gray-100 mb-2">
                        <h3 class="font-medium text-sm truncate"><?= e($store['name']) ?></h3>
                        <span class="text-xs text-indigo-600 dark:text-indigo-400"><?= $store['active_coupons'] ?> <?= __('coupons', 'كوبونات') ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Coupons -->
            <h2 class="text-lg font-semibold mb-4"><?= __('Coupons', 'كوبونات') ?> (<?= $totalCoupons ?>)</h2>
            
            <?php if (empty($coupons)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                <i class="fas fa-ticket-alt text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400"><?= __('No coupons in this category', 'لا توجد كوبونات في هذه الفئة') ?></h3>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            </div>
                            <span class="bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-200 px-2 py-1 rounded text-xs font-bold">
                                <?= formatDiscount($coupon) ?>
                            </span>
                        </div>
                        
                        <h3 class="font-medium text-gray-900 dark:text-white line-clamp-2">
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
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?= paginate($currentPage, $totalPages, url('category/' . $category['slug'])) ?>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-80 flex-shrink-0">
            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-8 text-center">
                <p class="text-gray-400 text-sm"><?= __('Advertisement', 'إعلان') ?></p>
            </div>
        </aside>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
