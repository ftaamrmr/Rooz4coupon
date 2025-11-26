<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([__('Home', 'الرئيسية') => url('/'), getLocalizedField($page, 'title') => '']) ?>
    
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8"><?= e(getLocalizedField($page, 'title')) ?></h1>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-md">
            <div class="prose dark:prose-invert max-w-none">
                <?= getLocalizedField($page, 'content') ?>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
