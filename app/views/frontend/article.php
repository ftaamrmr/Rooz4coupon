<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Article Schema.org Markup -->
<?php
echo schemaJsonLd('Article', [
    'headline' => $article['title'],
    'description' => $article['excerpt'] ?? truncate(strip_tags($article['content']), 160),
    'image' => $article['cover_image'] ? UPLOAD_URL . '/' . $article['cover_image'] : '',
    'datePublished' => $article['publish_date'],
    'dateModified' => $article['updated_at'],
    'author' => [
        '@type' => 'Person',
        'name' => $article['author_full_name'] ?? $article['author_name']
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => getSetting('site_name', 'CouponHub'),
        'logo' => getSetting('logo') ? UPLOAD_URL . '/' . getSetting('logo') : ''
    ]
]);
?>

<!-- Breadcrumbs -->
<div class="bg-gray-100 dark:bg-gray-800 py-4">
    <div class="container mx-auto px-4">
        <?php
        echo breadcrumbs([
            ['title' => __('home'), 'url' => BASE_URL],
            ['title' => __('blog'), 'url' => BASE_URL . '/blog'],
            ['title' => truncate($article['title'], 50), 'url' => '']
        ]);
        ?>
    </div>
</div>

<!-- Article -->
<article class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Article Header -->
            <header class="mb-8">
                <?php if ($article['category_name']): ?>
                <a href="<?php echo BASE_URL; ?>/category/<?php echo $article['category_slug']; ?>" 
                   class="inline-block text-sm text-primary font-semibold uppercase tracking-wider mb-4">
                    <?php echo htmlspecialchars($article['category_name']); ?>
                </a>
                <?php endif; ?>
                
                <h1 class="text-3xl md:text-4xl font-bold mb-4">
                    <?php echo htmlspecialchars(isRTL() && $article['title_ar'] ? $article['title_ar'] : $article['title']); ?>
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <i class="far fa-user mr-2"></i>
                        <span><?php echo __('by'); ?> <?php echo htmlspecialchars($article['author_full_name'] ?? $article['author_name']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="far fa-calendar mr-2"></i>
                        <time datetime="<?php echo $article['publish_date']; ?>">
                            <?php echo __('published_on'); ?> <?php echo formatDate($article['publish_date'], 'F j, Y'); ?>
                        </time>
                    </div>
                    <div class="flex items-center">
                        <i class="far fa-eye mr-2"></i>
                        <span><?php echo number_format($article['views_count']); ?> views</span>
                    </div>
                </div>
            </header>
            
            <!-- Featured Image -->
            <?php if ($article['cover_image']): ?>
            <figure class="mb-8">
                <img src="<?php echo UPLOAD_URL . '/' . $article['cover_image']; ?>" 
                     alt="<?php echo htmlspecialchars($article['title']); ?>"
                     class="w-full rounded-xl shadow-lg">
            </figure>
            <?php endif; ?>
            
            <!-- Ad Slot -->
            <?php if (getSetting('ad_article_top')): ?>
            <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 mb-8 text-center">
                <?php echo getSetting('ad_article_top'); ?>
            </div>
            <?php endif; ?>
            
            <!-- Article Content -->
            <div class="prose prose-lg dark:prose-invert max-w-none mb-8">
                <?php echo isRTL() && $article['content_ar'] ? $article['content_ar'] : $article['content']; ?>
            </div>
            
            <!-- Tags -->
            <?php if (!empty($tags)): ?>
            <div class="mb-8">
                <h3 class="font-semibold mb-3"><?php echo __('tags'); ?>:</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm">
                        #<?php echo htmlspecialchars($tag['name']); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Share Buttons -->
            <div class="border-t border-b dark:border-gray-700 py-6 mb-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <span class="font-semibold"><?php echo __('share'); ?> this article:</span>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(currentUrl()); ?>" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(currentUrl()); ?>&text=<?php echo urlencode($article['title']); ?>" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(currentUrl()); ?>&title=<?php echo urlencode($article['title']); ?>" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center bg-blue-800 text-white rounded-lg hover:bg-blue-900 transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' ' . currentUrl()); ?>" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Ad Slot -->
            <?php if (getSetting('ad_article_bottom')): ?>
            <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 mb-8 text-center">
                <?php echo getSetting('ad_article_bottom'); ?>
            </div>
            <?php endif; ?>
            
            <!-- Related Articles -->
            <?php if (!empty($relatedArticles)): ?>
            <section>
                <h2 class="text-2xl font-bold mb-6"><?php echo __('related_articles'); ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($relatedArticles as $related): ?>
                    <article class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow hover:shadow-lg transition flex">
                        <?php if ($related['cover_image']): ?>
                        <a href="<?php echo BASE_URL; ?>/blog/<?php echo $related['slug']; ?>" class="w-32 flex-shrink-0">
                            <img src="<?php echo UPLOAD_URL . '/' . $related['cover_image']; ?>" alt="" class="w-full h-full object-cover">
                        </a>
                        <?php endif; ?>
                        <div class="p-4 flex flex-col justify-center">
                            <h3 class="font-semibold line-clamp-2 hover:text-primary transition">
                                <a href="<?php echo BASE_URL; ?>/blog/<?php echo $related['slug']; ?>">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 mt-2"><?php echo formatDate($related['publish_date']); ?></p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>
</article>

<!-- TailwindCSS Typography Prose Styles -->
<style>
.prose { color: inherit; }
.prose h1, .prose h2, .prose h3, .prose h4 { color: inherit; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; }
.prose h2 { font-size: 1.5rem; }
.prose h3 { font-size: 1.25rem; }
.prose p { margin-bottom: 1rem; line-height: 1.75; }
.prose a { color: #3b82f6; text-decoration: underline; }
.prose ul, .prose ol { margin-bottom: 1rem; padding-left: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }
.prose blockquote { border-left: 4px solid #3b82f6; padding-left: 1rem; font-style: italic; margin: 1.5rem 0; }
.prose pre { background: #1f2937; color: #e5e7eb; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 1.5rem 0; }
.prose code { background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; }
.dark .prose code { background: #374151; }
.prose img { border-radius: 0.5rem; margin: 1.5rem 0; }
.prose table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
.prose th, .prose td { border: 1px solid #e5e7eb; padding: 0.75rem; text-align: left; }
.dark .prose th, .dark .prose td { border-color: #374151; }
.prose th { background: #f9fafb; }
.dark .prose th { background: #374151; }
</style>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
