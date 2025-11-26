<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-16">
    <div class="max-w-lg mx-auto text-center">
        <div class="text-9xl font-bold gradient-text mb-4">404</div>
        <h1 class="text-2xl font-bold mb-4"><?= __('Page Not Found', 'الصفحة غير موجودة') ?></h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            <?= __("The page you're looking for doesn't exist or has been moved.", 'الصفحة التي تبحث عنها غير موجودة أو تم نقلها.') ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= url('/') ?>" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-home mr-2"></i><?= __('Go Home', 'الرئيسية') ?>
            </a>
            <a href="<?= url('stores') ?>" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                <i class="fas fa-store mr-2"></i><?= __('Browse Stores', 'تصفح المتاجر') ?>
            </a>
        </div>
        
        <!-- Search -->
        <div class="mt-12">
            <p class="text-gray-500 mb-4"><?= __('Or try searching:', 'أو جرب البحث:') ?></p>
            <form action="<?= url('search') ?>" method="GET" class="flex max-w-md mx-auto">
                <input type="text" name="q" placeholder="<?= __('Search...', 'بحث...') ?>"
                       class="flex-1 px-4 py-2 border rounded-l-lg dark:bg-gray-700 dark:border-gray-600">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
