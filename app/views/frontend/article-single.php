<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<article class="container mx-auto px-4 py-8">
    <?= breadcrumbs([
        __('Home', 'الرئيسية') => url('/'), 
        __('Blog', 'المدونة') => url('blog'),
        getLocalizedField($article, 'title') => ''
    ]) ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1 max-w-3xl">
            <!-- Article Header -->
            <header class="mb-8">
                <?php if ($article['category_name']): ?>
                <a href="<?= url('blog/category/' . $article['category_slug']) ?>" 
                   class="inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline mb-3">
                    <?= e($article['category_name']) ?>
                </a>
                <?php endif; ?>
                
                <h1 class="text-3xl md:text-4xl font-bold mb-4"><?= e(getLocalizedField($article, 'title')) ?></h1>
                
                <div class="flex flex-wrap items-center gap-4 text-gray-500 dark:text-gray-400">
                    <?php if ($article['author_name']): ?>
                    <div class="flex items-center gap-2">
                        <?php if ($article['author_avatar']): ?>
                        <img src="<?= upload($article['author_avatar']) ?>" alt="" class="w-8 h-8 rounded-full">
                        <?php else: ?>
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <?php endif; ?>
                        <span><?= e($article['author_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <span><i class="far fa-calendar mr-1"></i> <?= formatDate($article['published_at']) ?></span>
                    <span><i class="far fa-eye mr-1"></i> <?= $article['views_count'] ?> <?= __('views', 'مشاهدة') ?></span>
                </div>
            </header>
            
            <!-- Featured Image -->
            <?php if ($article['featured_image']): ?>
            <img src="<?= upload($article['featured_image']) ?>" 
                 alt="<?= e(getLocalizedField($article, 'title')) ?>" 
                 class="w-full rounded-xl shadow-lg mb-8">
            <?php endif; ?>
            
            <!-- Article Content -->
            <div class="prose prose-lg dark:prose-invert max-w-none mb-8">
                <?= getLocalizedField($article, 'content') ?>
            </div>
            
            <!-- Tags -->
            <?php if (!empty($tags)): ?>
            <div class="flex flex-wrap items-center gap-2 mb-8 pt-6 border-t dark:border-gray-700">
                <span class="text-gray-500"><i class="fas fa-tags mr-1"></i> <?= __('Tags', 'الوسوم') ?>:</span>
                <?php foreach ($tags as $tag): ?>
                <a href="<?= url('blog/tag/' . $tag['slug']) ?>" 
                   class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm hover:bg-indigo-100 dark:hover:bg-indigo-900">
                    <?= e(getLocalizedField($tag, 'name')) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Share -->
            <div class="flex items-center gap-4 mb-8 pb-8 border-b dark:border-gray-700">
                <span class="text-gray-500"><?= __('Share', 'مشاركة') ?>:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('blog/' . $article['slug'])) ?>" 
                   target="_blank" class="text-blue-600 hover:text-blue-700">
                    <i class="fab fa-facebook-f text-xl"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(url('blog/' . $article['slug'])) ?>&text=<?= urlencode(getLocalizedField($article, 'title')) ?>" 
                   target="_blank" class="text-sky-500 hover:text-sky-600">
                    <i class="fab fa-twitter text-xl"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(url('blog/' . $article['slug'])) ?>" 
                   target="_blank" class="text-blue-700 hover:text-blue-800">
                    <i class="fab fa-linkedin-in text-xl"></i>
                </a>
                <a href="https://wa.me/?text=<?= urlencode(getLocalizedField($article, 'title') . ' ' . url('blog/' . $article['slug'])) ?>" 
                   target="_blank" class="text-green-500 hover:text-green-600">
                    <i class="fab fa-whatsapp text-xl"></i>
                </a>
            </div>
            
            <!-- Prev/Next Navigation -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <?php if ($prevArticle): ?>
                <a href="<?= url('blog/' . $prevArticle['slug']) ?>" 
                   class="flex-1 p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-md group">
                    <span class="text-sm text-gray-500"><i class="fas fa-arrow-left mr-1"></i> <?= __('Previous', 'السابق') ?></span>
                    <h4 class="font-medium mt-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 line-clamp-1">
                        <?= e(getLocalizedField($prevArticle, 'title')) ?>
                    </h4>
                </a>
                <?php endif; ?>
                
                <?php if ($nextArticle): ?>
                <a href="<?= url('blog/' . $nextArticle['slug']) ?>" 
                   class="flex-1 p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-md group text-right">
                    <span class="text-sm text-gray-500"><?= __('Next', 'التالي') ?> <i class="fas fa-arrow-right ml-1"></i></span>
                    <h4 class="font-medium mt-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 line-clamp-1">
                        <?= e(getLocalizedField($nextArticle, 'title')) ?>
                    </h4>
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Related Articles -->
            <?php if (!empty($relatedArticles)): ?>
            <div class="mt-12">
                <h2 class="text-xl font-bold mb-6"><?= __('Related Articles', 'مقالات ذات صلة') ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($relatedArticles as $related): ?>
                    <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <?php if ($related['featured_image']): ?>
                        <img src="<?= upload($related['featured_image']) ?>" 
                             alt="<?= e(getLocalizedField($related, 'title')) ?>" 
                             class="w-full h-40 object-cover">
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="font-semibold line-clamp-2 mb-2">
                                <a href="<?= url('blog/' . $related['slug']) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                    <?= e(getLocalizedField($related, 'title')) ?>
                                </a>
                            </h3>
                            <div class="text-sm text-gray-500"><?= formatDate($related['published_at']) ?></div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-80 flex-shrink-0">
            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-8 text-center sticky top-24">
                <p class="text-gray-400 text-sm"><?= __('Advertisement', 'إعلان') ?></p>
            </div>
        </aside>
    </div>
</article>

<!-- Schema.org markup for Article -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": <?= json_encode(getLocalizedField($article, 'title')) ?>,
    "description": <?= json_encode(getLocalizedField($article, 'excerpt')) ?>,
    "datePublished": <?= json_encode($article['published_at']) ?>,
    "dateModified": <?= json_encode($article['updated_at']) ?>,
    "author": {
        "@type": "Person",
        "name": <?= json_encode($article['author_name'] ?: 'Admin') ?>
    }
    <?php if ($article['featured_image']): ?>
    ,"image": <?= json_encode(upload($article['featured_image'])) ?>
    <?php endif; ?>
}
</script>

<style>
    .prose h2 { @apply text-2xl font-bold mt-8 mb-4; }
    .prose h3 { @apply text-xl font-bold mt-6 mb-3; }
    .prose p { @apply mb-4; }
    .prose ul, .prose ol { @apply mb-4 pl-6; }
    .prose ul { @apply list-disc; }
    .prose ol { @apply list-decimal; }
    .prose li { @apply mb-2; }
    .prose a { @apply text-indigo-600 hover:underline; }
    .prose blockquote { @apply border-l-4 border-indigo-500 pl-4 italic my-4; }
    .prose pre { @apply bg-gray-800 text-gray-100 rounded-lg p-4 overflow-x-auto my-4; }
    .prose code { @apply bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm; }
    .prose pre code { @apply bg-transparent p-0; }
    .prose img { @apply rounded-lg my-4; }
    .prose table { @apply w-full border-collapse my-4; }
    .prose th, .prose td { @apply border dark:border-gray-600 px-4 py-2; }
    .prose th { @apply bg-gray-100 dark:bg-gray-700; }
</style>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
