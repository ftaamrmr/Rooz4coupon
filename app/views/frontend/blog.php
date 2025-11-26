<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([__('Home', 'الرئيسية') => url('/'), __('Blog', 'المدونة') => '']) ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-8"><?= __('Blog', 'المدونة') ?></h1>
            
            <!-- Featured Articles -->
            <?php if (!empty($featuredArticles)): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <?php foreach ($featuredArticles as $i => $article): ?>
                <?php if ($i === 0): ?>
                <article class="md:col-span-2 md:row-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <?php if ($article['featured_image']): ?>
                    <img src="<?= upload($article['featured_image']) ?>" 
                         alt="<?= e(getLocalizedField($article, 'title')) ?>" 
                         class="w-full h-64 object-cover">
                    <?php else: ?>
                    <div class="w-full h-64 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-white/30"></i>
                    </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <span class="text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 px-2 py-1 rounded">
                            <?= __('Featured', 'مميز') ?>
                        </span>
                        <h2 class="text-xl font-bold mt-3 mb-2">
                            <a href="<?= url('blog/' . $article['slug']) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= e(getLocalizedField($article, 'title')) ?>
                            </a>
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            <?= e(truncate(getLocalizedField($article, 'excerpt'), 200)) ?>
                        </p>
                        <div class="text-sm text-gray-500">
                            <?= formatDate($article['published_at']) ?>
                            <?php if ($article['author_name']): ?>
                             • <?= e($article['author_name']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
                <?php else: ?>
                <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <?php if ($article['featured_image']): ?>
                    <img src="<?= upload($article['featured_image']) ?>" 
                         alt="<?= e(getLocalizedField($article, 'title')) ?>" 
                         class="w-full h-32 object-cover">
                    <?php endif; ?>
                    <div class="p-4">
                        <h3 class="font-semibold mb-2 line-clamp-2">
                            <a href="<?= url('blog/' . $article['slug']) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= e(getLocalizedField($article, 'title')) ?>
                            </a>
                        </h3>
                        <div class="text-xs text-gray-500"><?= formatDate($article['published_at']) ?></div>
                    </div>
                </article>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- All Articles -->
            <h2 class="text-xl font-bold mb-6"><?= __('Latest Articles', 'أحدث المقالات') ?></h2>
            
            <?php if (empty($articles)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                <i class="fas fa-newspaper text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400"><?= __('No articles found', 'لا توجد مقالات') ?></h3>
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
                    <?php endif; ?>
                    <div class="p-5">
                        <?php if ($article['category_name']): ?>
                        <a href="<?= url('blog/category/' . $article['category_slug']) ?>" 
                           class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            <?= e($article['category_name']) ?>
                        </a>
                        <?php endif; ?>
                        
                        <h3 class="font-semibold text-lg mt-2 mb-2 line-clamp-2">
                            <a href="<?= url('blog/' . $article['slug']) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= e(getLocalizedField($article, 'title')) ?>
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-4">
                            <?= e(truncate(getLocalizedField($article, 'excerpt'), 120)) ?>
                        </p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span><?= formatDate($article['published_at']) ?></span>
                            <span><i class="far fa-eye mr-1"></i> <?= $article['views_count'] ?></span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            
            <?= paginate($currentPage, $totalPages, url('blog')) ?>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-80 flex-shrink-0 space-y-6">
            <!-- Search -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                <h3 class="font-semibold mb-4"><?= __('Search Articles', 'بحث في المقالات') ?></h3>
                <form action="<?= url('search') ?>" method="GET">
                    <input type="hidden" name="type" value="articles">
                    <div class="flex">
                        <input type="text" name="q" placeholder="<?= __('Search...', 'بحث...') ?>"
                               class="flex-1 px-4 py-2 border rounded-l-lg dark:bg-gray-700 dark:border-gray-600">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Categories -->
            <?php if (!empty($categories)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                <h3 class="font-semibold mb-4"><?= __('Categories', 'التصنيفات') ?></h3>
                <ul class="space-y-2">
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="<?= url('blog/category/' . $cat['slug']) ?>" 
                           class="flex justify-between items-center py-2 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <span><?= e(getLocalizedField($cat, 'name')) ?></span>
                            <span class="text-sm text-gray-500">(<?= $cat['articles_count'] ?>)</span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Tags -->
            <?php if (!empty($tags)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                <h3 class="font-semibold mb-4"><?= __('Popular Tags', 'الوسوم الشائعة') ?></h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                    <a href="<?= url('blog/tag/' . $tag['slug']) ?>" 
                       class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm hover:bg-indigo-100 dark:hover:bg-indigo-900">
                        <?= e(getLocalizedField($tag, 'name')) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Ad -->
            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-8 text-center">
                <p class="text-gray-400 text-sm"><?= __('Advertisement', 'إعلان') ?></p>
            </div>
        </aside>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
