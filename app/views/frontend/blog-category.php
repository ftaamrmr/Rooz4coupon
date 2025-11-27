<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([
        __('Home', 'الرئيسية') => url('/'), 
        __('Blog', 'المدونة') => url('blog'),
        getLocalizedField($category, 'name') => ''
    ]) ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <h1 class="text-3xl font-bold mb-6">
                <?= e(getLocalizedField($category, 'name')) ?>
            </h1>
            
            <?php if (!empty(getLocalizedField($category, 'description'))): ?>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                <?= e(getLocalizedField($category, 'description')) ?>
            </p>
            <?php endif; ?>
            
            <?php if (empty($articles)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                <i class="fas fa-newspaper text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400">
                    <?= __('No articles in this category yet', 'لا توجد مقالات في هذه الفئة بعد') ?>
                </h3>
            </div>
            <?php else: ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($articles as $article): ?>
                <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if ($article['featured_image']): ?>
                    <a href="<?= url('blog/' . $article['slug']) ?>">
                        <img src="<?= upload($article['featured_image']) ?>" 
                             alt="<?= e(getLocalizedField($article, 'title')) ?>" 
                             class="w-full h-48 object-cover">
                    </a>
                    <?php else: ?>
                    <a href="<?= url('blog/' . $article['slug']) ?>" 
                       class="block w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-600 
                              flex items-center justify-center">
                        <i class="fas fa-newspaper text-4xl text-white/50"></i>
                    </a>
                    <?php endif; ?>
                    
                    <div class="p-5">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                            <span><?= formatDate($article['published_at']) ?></span>
                            <?php if ($article['author_name']): ?>
                            <span class="mx-2">•</span>
                            <span><?= e($article['author_name']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <h2 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">
                            <a href="<?= url('blog/' . $article['slug']) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= e(getLocalizedField($article, 'title')) ?>
                            </a>
                        </h2>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                            <?= e(truncate(getLocalizedField($article, 'excerpt'), 150)) ?>
                        </p>
                        
                        <a href="<?= url('blog/' . $article['slug']) ?>" class="inline-block mt-4 text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            <?= __('Read More', 'اقرأ المزيد') ?> →
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            
            <?= paginate($currentPage, $totalPages, url('blog/category/' . $category['slug'])) ?>
            
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-80 flex-shrink-0 space-y-6">
            <!-- Search -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                <h3 class="font-semibold mb-4"><?= __('Search Articles', 'البحث في المقالات') ?></h3>
                <form action="<?= url('search') ?>" method="GET">
                    <input type="hidden" name="type" value="articles">
                    <input type="text" name="q" placeholder="<?= __('Search...', 'بحث...') ?>"
                           class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                </form>
            </div>
            
            <!-- Back to Blog -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                <a href="<?= url('blog') ?>" class="flex items-center text-indigo-600 dark:text-indigo-400 hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <?= __('Back to Blog', 'العودة للمدونة') ?>
                </a>
            </div>
            
            <!-- Ad Placeholder -->
            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-8 text-center">
                <p class="text-gray-400 text-sm"><?= __('Advertisement', 'إعلان') ?></p>
            </div>
        </aside>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
