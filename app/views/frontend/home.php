<?php include APP_PATH . '/views/frontend/partials/header.php'; ?>

<!-- Hero Section -->
<section class="gradient-bg py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center text-white">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">
                <?= e(getSetting('hero_title_' . getCurrentLang(), __('Find the Best Deals & Coupons', 'اعثر على أفضل العروض والكوبونات'))) ?>
            </h1>
            <p class="text-lg md:text-xl opacity-90 mb-8">
                <?= e(getSetting('hero_subtitle_' . getCurrentLang(), __('Save money with exclusive discount codes from your favorite stores', 'وفر المال مع أكواد الخصم الحصرية من متاجرك المفضلة'))) ?>
            </p>
            
            <!-- Search Form -->
            <form action="<?= url('search') ?>" method="GET" class="max-w-xl mx-auto">
                <div class="flex bg-white rounded-lg shadow-lg overflow-hidden">
                    <input type="text" name="q" placeholder="<?= __('Search stores, coupons, deals...', 'ابحث عن متاجر، كوبونات، عروض...') ?>" 
                           class="flex-1 px-6 py-4 text-gray-900 focus:outline-none">
                    <button type="submit" class="px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                        <i class="fas fa-search mr-2"></i><?= __('Search', 'بحث') ?>
                    </button>
                </div>
            </form>
            
            <!-- Stats -->
            <div class="flex flex-wrap justify-center gap-8 mt-10">
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['total_coupons']) ?>+</div>
                    <div class="text-sm opacity-75"><?= __('Active Coupons', 'كوبونات نشطة') ?></div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['total_stores']) ?>+</div>
                    <div class="text-sm opacity-75"><?= __('Partner Stores', 'متاجر شريكة') ?></div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['active_deals']) ?>+</div>
                    <div class="text-sm opacity-75"><?= __('Daily Deals', 'عروض يومية') ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Slider -->
<section class="py-12 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold"><?= __('Browse Categories', 'تصفح الفئات') ?></h2>
            <a href="<?= url('categories') ?>" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium">
                <?= __('View All', 'عرض الكل') ?> <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach ($categories as $category): ?>
            <a href="<?= url('category/' . $category['slug']) ?>" 
               class="group bg-gray-50 dark:bg-gray-700 rounded-xl p-6 text-center hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 mx-auto mb-3 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800">
                    <i class="fas <?= e($category['icon'] ?: 'fa-tag') ?> text-2xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <h3 class="font-medium text-gray-900 dark:text-white"><?= e(getLocalizedField($category, 'name')) ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Coupons -->
<?php if (!empty($featuredCoupons)): ?>
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold">
                <i class="fas fa-star text-yellow-500 mr-2"></i><?= __('Featured Deals', 'العروض المميزة') ?>
            </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredCoupons as $coupon): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <img src="<?= getStoreLogo($coupon['store_logo'], $coupon['store_name']) ?>" 
                             alt="<?= e($coupon['store_name']) ?>" 
                             class="w-16 h-16 rounded-lg object-cover bg-gray-100">
                        <div class="flex-1">
                            <a href="<?= url('store/' . $coupon['store_slug']) ?>" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                <?= e($coupon['store_name']) ?>
                            </a>
                            <h3 class="font-semibold text-gray-900 dark:text-white mt-1">
                                <?= e(getLocalizedField($coupon, 'title')) ?>
                            </h3>
                        </div>
                        <span class="bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-200 px-3 py-1 rounded-full text-sm font-bold">
                            <?= formatDiscount($coupon) ?>
                        </span>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-3 line-clamp-2">
                        <?= e(truncate(getLocalizedField($coupon, 'description'), 100)) ?>
                    </p>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t dark:border-gray-700">
                        <?php if (!empty($coupon['code'])): ?>
                        <div class="coupon-code bg-gray-50 dark:bg-gray-700 px-4 py-2 rounded text-center font-mono text-sm">
                            <?= e($coupon['code']) ?>
                        </div>
                        <button onclick="copyCode('<?= e($coupon['code']) ?>', this)" 
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-copy mr-1"></i> <?= __('Copy', 'نسخ') ?>
                        </button>
                        <?php else: ?>
                        <a href="<?= e($coupon['affiliate_url'] ?: '#') ?>" target="_blank"
                           class="flex-1 text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                            <?= __('Get Deal', 'احصل على العرض') ?> <i class="fas fa-external-link-alt ml-1"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($coupon['expiry_date']): ?>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">
                        <i class="far fa-clock mr-1"></i> <?= generateCountdown($coupon['expiry_date']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Stores -->
<?php if (!empty($featuredStores)): ?>
<section class="py-12 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold"><?= __('Featured Stores', 'المتاجر المميزة') ?></h2>
            <a href="<?= url('stores') ?>" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium">
                <?= __('View All Stores', 'عرض جميع المتاجر') ?> <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
            <?php foreach ($featuredStores as $store): ?>
            <a href="<?= url('store/' . $store['slug']) ?>" 
               class="group bg-gray-50 dark:bg-gray-700 rounded-xl p-4 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <img src="<?= getStoreLogo($store['logo'], $store['name']) ?>" 
                     alt="<?= e($store['name']) ?>" 
                     class="w-16 h-16 mx-auto rounded-lg object-cover bg-white mb-3">
                <h3 class="font-medium text-sm text-gray-900 dark:text-white truncate"><?= e($store['name']) ?></h3>
                <span class="text-xs text-indigo-600 dark:text-indigo-400"><?= $store['coupons_count'] ?> <?= __('Coupons', 'كوبونات') ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Coupons -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold"><?= __('Latest Coupons', 'أحدث الكوبونات') ?></h2>
            <a href="<?= url('coupons') ?>" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium">
                <?= __('View All', 'عرض الكل') ?> <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($latestCoupons as $coupon): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <img src="<?= getStoreLogo($coupon['store_logo'], $coupon['store_name']) ?>" 
                             alt="<?= e($coupon['store_name']) ?>" 
                             class="w-12 h-12 rounded-lg object-cover bg-gray-100">
                        <div class="flex-1 min-w-0">
                            <a href="<?= url('store/' . $coupon['store_slug']) ?>" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                <?= e($coupon['store_name']) ?>
                            </a>
                            <div class="flex items-center gap-2">
                                <?php if ($coupon['is_verified']): ?>
                                <span class="text-xs text-green-600 dark:text-green-400"><i class="fas fa-check-circle"></i> <?= __('Verified', 'موثق') ?></span>
                                <?php endif; ?>
                                <?php if ($coupon['is_exclusive']): ?>
                                <span class="text-xs text-purple-600 dark:text-purple-400"><i class="fas fa-gem"></i> <?= __('Exclusive', 'حصري') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-200 px-2 py-1 rounded text-xs font-bold">
                            <?= formatDiscount($coupon) ?>
                        </span>
                    </div>
                    
                    <h3 class="font-medium text-gray-900 dark:text-white line-clamp-2 min-h-[48px]">
                        <?= e(getLocalizedField($coupon, 'title')) ?>
                    </h3>
                    
                    <div class="flex items-center justify-between mt-4">
                        <?php if (!empty($coupon['code'])): ?>
                        <button onclick="copyCode('<?= e($coupon['code']) ?>', this)" 
                                class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-copy mr-1"></i> <?= __('Copy Code', 'نسخ الكود') ?>
                        </button>
                        <?php else: ?>
                        <a href="<?= e($coupon['affiliate_url'] ?: url('coupon/' . $coupon['id'])) ?>" 
                           class="flex-1 text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                            <?= __('Get Deal', 'احصل على العرض') ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($coupon['expiry_date']): ?>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                        <i class="far fa-clock mr-1"></i> 
                        <span data-countdown="<?= e($coupon['expiry_date']) ?>"><?= generateCountdown($coupon['expiry_date']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popular Stores -->
<?php if (!empty($popularStores)): ?>
<section class="py-12 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold"><?= __('Popular Stores', 'المتاجر الشائعة') ?></h2>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
            <?php foreach ($popularStores as $store): ?>
            <a href="<?= url('store/' . $store['slug']) ?>" 
               class="group bg-gray-50 dark:bg-gray-700 rounded-xl p-4 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <img src="<?= getStoreLogo($store['logo'], $store['name']) ?>" 
                     alt="<?= e($store['name']) ?>" 
                     class="w-16 h-16 mx-auto rounded-lg object-cover bg-white mb-3">
                <h3 class="font-medium text-sm text-gray-900 dark:text-white truncate"><?= e($store['name']) ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Articles -->
<?php if (!empty($latestArticles)): ?>
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold"><?= __('Latest from Blog', 'أحدث المقالات') ?></h2>
            <a href="<?= url('blog') ?>" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium">
                <?= __('View All', 'عرض الكل') ?> <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($latestArticles as $article): ?>
            <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <?php if ($article['featured_image']): ?>
                <img src="<?= upload($article['featured_image']) ?>" 
                     alt="<?= e(getLocalizedField($article, 'title')) ?>" 
                     class="w-full h-48 object-cover">
                <?php else: ?>
                <div class="w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-newspaper text-4xl text-white/50"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-5">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        <span><?= formatDate($article['published_at']) ?></span>
                        <?php if ($article['author_name']): ?>
                        <span class="mx-2">•</span>
                        <span><?= e($article['author_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 mb-2">
                        <a href="<?= url('blog/' . $article['slug']) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            <?= e(getLocalizedField($article, 'title')) ?>
                        </a>
                    </h3>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                        <?= e(truncate(getLocalizedField($article, 'excerpt'), 100)) ?>
                    </p>
                    
                    <a href="<?= url('blog/' . $article['slug']) ?>" class="inline-block mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        <?= __('Read More', 'اقرأ المزيد') ?> →
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Ad Placeholder -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-8 text-center">
            <p class="text-gray-400 text-sm"><?= __('Advertisement Space', 'مساحة إعلانية') ?></p>
            <!-- Google AdSense placeholder -->
            <!-- <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXX" data-ad-slot="XXXXXX"></ins> -->
        </div>
    </div>
</section>

<?php include APP_PATH . '/views/frontend/partials/footer.php'; ?>
