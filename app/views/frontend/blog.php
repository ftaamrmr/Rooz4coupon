<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Page Header -->
<section class="gradient-bg py-12">
    <div class="container mx-auto px-4 text-center text-white">
        <h1 class="text-3xl font-bold mb-2"><?php echo __('blog'); ?></h1>
        <p class="text-gray-100">Tips, tricks, and news about saving money</p>
    </div>
</section>

<!-- Blog Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <?php if (!empty($articles)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($articles as $article): ?>
            <article class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow hover:shadow-lg transition group">
                <?php if ($article['cover_image']): ?>
                <a href="<?php echo BASE_URL; ?>/blog/<?php echo $article['slug']; ?>" class="block overflow-hidden">
                    <img src="<?php echo UPLOAD_URL . '/' . $article['cover_image']; ?>" 
                         alt="<?php echo htmlspecialchars($article['title']); ?>" 
                         class="w-full h-52 object-cover group-hover:scale-105 transition duration-300">
                </a>
                <?php endif; ?>
                <div class="p-6">
                    <?php if ($article['category_name']): ?>
                    <a href="<?php echo BASE_URL; ?>/category/<?php echo $article['category_slug']; ?>" 
                       class="inline-block text-xs text-primary font-semibold uppercase tracking-wider mb-2">
                        <?php echo htmlspecialchars($article['category_name']); ?>
                    </a>
                    <?php endif; ?>
                    
                    <h2 class="text-xl font-bold mb-3 line-clamp-2 group-hover:text-primary transition">
                        <a href="<?php echo BASE_URL; ?>/blog/<?php echo $article['slug']; ?>">
                            <?php echo htmlspecialchars(isRTL() && $article['title_ar'] ? $article['title_ar'] : $article['title']); ?>
                        </a>
                    </h2>
                    
                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-4">
                        <?php echo htmlspecialchars($article['excerpt'] ?? truncate(strip_tags($article['content']), 150)); ?>
                    </p>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <i class="far fa-calendar mr-2"></i>
                            <time datetime="<?php echo $article['publish_date']; ?>"><?php echo formatDate($article['publish_date']); ?></time>
                        </div>
                        <div class="flex items-center">
                            <i class="far fa-eye mr-2"></i>
                            <span><?php echo number_format($article['views_count']); ?> views</span>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php echo paginationHtml($pagination, '/blog'); ?>
        <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
            <i class="fas fa-newspaper text-5xl text-gray-400 mb-4"></i>
            <p class="text-xl text-gray-500">No articles found</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
