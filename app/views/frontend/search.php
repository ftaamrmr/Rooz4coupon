<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <?= breadcrumbs([__('Home', 'الرئيسية') => url('/'), __('Search', 'بحث') => '']) ?>
    
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6"><?= __('Search Results', 'نتائج البحث') ?></h1>
        
        <!-- Search Form -->
        <form action="<?= url('search') ?>" method="GET" class="mb-8">
            <div class="flex gap-2">
                <input type="text" name="q" value="<?= e($query) ?>" 
                       placeholder="<?= __('Search for stores, coupons, articles...', 'ابحث عن متاجر، كوبونات، مقالات...') ?>"
                       class="flex-1 px-4 py-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-lg">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-search mr-2"></i><?= __('Search', 'بحث') ?>
                </button>
            </div>
            
            <!-- Type Filter -->
            <div class="flex gap-4 mt-4">
                <label class="flex items-center gap-2">
                    <input type="radio" name="type" value="all" <?= $type === 'all' ? 'checked' : '' ?> class="text-indigo-600">
                    <?= __('All', 'الكل') ?>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="type" value="coupons" <?= $type === 'coupons' ? 'checked' : '' ?> class="text-indigo-600">
                    <?= __('Coupons', 'كوبونات') ?>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="type" value="stores" <?= $type === 'stores' ? 'checked' : '' ?> class="text-indigo-600">
                    <?= __('Stores', 'متاجر') ?>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="type" value="articles" <?= $type === 'articles' ? 'checked' : '' ?> class="text-indigo-600">
                    <?= __('Articles', 'مقالات') ?>
                </label>
            </div>
        </form>
        
        <?php if (empty($query)): ?>
        <div class="text-center py-12">
            <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400"><?= __('Enter a search term to find results', 'أدخل كلمة للبحث') ?></p>
        </div>
        <?php elseif ($totalResults === 0): ?>
        <div class="text-center py-12">
            <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">
                <?= __('No results found for', 'لا توجد نتائج لـ') ?> "<?= e($query) ?>"
            </h3>
            <p class="text-gray-500"><?= __('Try different keywords or check spelling', 'جرب كلمات مختلفة أو تحقق من الإملاء') ?></p>
        </div>
        <?php else: ?>
        
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            <?= sprintf(__('Found %d results for "%s"', 'تم العثور على %d نتيجة لـ "%s"'), $totalResults, $query) ?>
        </p>
        
        <!-- Stores Results -->
        <?php if (!empty($results['stores'])): ?>
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4"><i class="fas fa-store mr-2"></i><?= __('Stores', 'المتاجر') ?></h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($results['stores'] as $store): ?>
                <a href="<?= url('store/' . $store['slug']) ?>" 
                   class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center shadow hover:shadow-md">
                    <img src="<?= getStoreLogo($store['logo'], $store['name']) ?>" 
                         alt="<?= e($store['name']) ?>" 
                         class="w-16 h-16 mx-auto rounded-lg object-cover bg-gray-100 mb-2">
                    <h3 class="font-medium truncate"><?= e($store['name']) ?></h3>
                    <span class="text-sm text-indigo-600 dark:text-indigo-400"><?= $store['active_coupons'] ?> <?= __('coupons', 'كوبونات') ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Coupons Results -->
        <?php if (!empty($results['coupons'])): ?>
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4"><i class="fas fa-ticket-alt mr-2"></i><?= __('Coupons', 'كوبونات') ?></h2>
            <div class="space-y-4">
                <?php foreach ($results['coupons'] as $coupon): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow hover:shadow-md flex items-center gap-4">
                    <img src="<?= getStoreLogo($coupon['store_logo'], $coupon['store_name']) ?>" 
                         alt="<?= e($coupon['store_name']) ?>" 
                         class="w-12 h-12 rounded-lg object-cover bg-gray-100">
                    <div class="flex-1 min-w-0">
                        <a href="<?= url('store/' . $coupon['store_slug']) ?>" class="text-sm text-indigo-600 dark:text-indigo-400">
                            <?= e($coupon['store_name']) ?>
                        </a>
                        <h3 class="font-medium truncate"><?= e(getLocalizedField($coupon, 'title')) ?></h3>
                    </div>
                    <span class="bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-200 px-2 py-1 rounded text-sm font-bold">
                        <?= formatDiscount($coupon) ?>
                    </span>
                    <?php if (!empty($coupon['code'])): ?>
                    <button onclick="copyCode('<?= e($coupon['code']) ?>', this)" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
                        <i class="fas fa-copy mr-1"></i> <?= __('Copy', 'نسخ') ?>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Articles Results -->
        <?php if (!empty($results['articles'])): ?>
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4"><i class="fas fa-newspaper mr-2"></i><?= __('Articles', 'مقالات') ?></h2>
            <div class="space-y-4">
                <?php foreach ($results['articles'] as $article): ?>
                <article class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow hover:shadow-md">
                    <h3 class="font-semibold mb-2">
                        <a href="<?= url('blog/' . $article['slug']) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            <?= e(getLocalizedField($article, 'title')) ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">
                        <?= e(truncate(getLocalizedField($article, 'excerpt'), 150)) ?>
                    </p>
                    <div class="text-sm text-gray-500 mt-2">
                        <?= formatDate($article['published_at']) ?>
                        <?php if ($article['author_name']): ?> • <?= e($article['author_name']) ?><?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</div>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
