<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([__('Home', 'الرئيسية') => url('/'), __('Stores', 'المتاجر') => '']) ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md sticky top-24">
                <h3 class="font-semibold text-lg mb-4"><?= __('Filter by Category', 'تصفية حسب الفئة') ?></h3>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= url('stores') ?>" 
                           class="block py-2 px-3 rounded-lg <?= !$selectedCategory ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <?= __('All Categories', 'جميع الفئات') ?>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="<?= url('stores?category=' . $cat['id']) ?>" 
                           class="block py-2 px-3 rounded-lg <?= $selectedCategory == $cat['id'] ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                            <?= e(getLocalizedField($cat, 'name')) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h1 class="text-2xl font-bold"><?= __('All Stores', 'جميع المتاجر') ?> <span class="text-gray-500 font-normal">(<?= $totalStores ?>)</span></h1>
                
                <!-- Search -->
                <form action="<?= url('stores') ?>" method="GET" class="flex gap-2">
                    <?php if ($selectedCategory): ?>
                    <input type="hidden" name="category" value="<?= e($selectedCategory) ?>">
                    <?php endif; ?>
                    <input type="text" name="q" value="<?= e($searchQuery) ?>" 
                           placeholder="<?= __('Search stores...', 'بحث في المتاجر...') ?>"
                           class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <?php if (empty($stores)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                <i class="fas fa-store text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400"><?= __('No stores found', 'لا توجد متاجر') ?></h3>
            </div>
            <?php else: ?>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <?php foreach ($stores as $store): ?>
                <a href="<?= url('store/' . $store['slug']) ?>" 
                   class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-md hover:shadow-lg transition-all hover:-translate-y-1">
                    <img src="<?= getStoreLogo($store['logo'], $store['name']) ?>" 
                         alt="<?= e($store['name']) ?>" 
                         class="w-20 h-20 mx-auto rounded-xl object-cover bg-gray-100 mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white truncate"><?= e($store['name']) ?></h3>
                    <p class="text-sm text-indigo-600 dark:text-indigo-400 mt-1">
                        <?= $store['active_coupons'] ?> <?= __('Coupons', 'كوبونات') ?>
                    </p>
                    <?php if ($store['is_featured']): ?>
                    <span class="inline-block mt-2 text-xs bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200 px-2 py-1 rounded">
                        <i class="fas fa-star mr-1"></i><?= __('Featured', 'مميز') ?>
                    </span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <?= paginate($currentPage, $totalPages, url('stores')) ?>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
