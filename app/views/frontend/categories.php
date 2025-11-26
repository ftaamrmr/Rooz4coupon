<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([__('Home', 'الرئيسية') => url('/'), __('Categories', 'الفئات') => '']) ?>
    
    <h1 class="text-2xl font-bold mb-8"><?= __('All Categories', 'جميع الفئات') ?></h1>
    
    <?php if (empty($categories)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
        <i class="fas fa-folder text-4xl text-gray-400 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400"><?= __('No categories found', 'لا توجد فئات') ?></h3>
    </div>
    <?php else: ?>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($categories as $category): ?>
        <a href="<?= url('category/' . $category['slug']) ?>" 
           class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md hover:shadow-lg transition-all hover:-translate-y-1 group">
            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-4 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800">
                <i class="fas <?= e($category['icon'] ?: 'fa-tag') ?> text-2xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2"><?= e(getLocalizedField($category, 'name')) ?></h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                <?= e(getLocalizedField($category, 'description')) ?>
            </p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-indigo-600 dark:text-indigo-400"><?= $category['stores_count'] ?> <?= __('Stores', 'متاجر') ?></span>
                <span class="text-gray-500"><?= $category['coupons_count'] ?> <?= __('Coupons', 'كوبونات') ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    
    <?php endif; ?>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
