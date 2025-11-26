<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Breadcrumbs -->
<div class="bg-gray-100 dark:bg-gray-800 py-4">
    <div class="container mx-auto px-4">
        <?php
        echo breadcrumbs([
            ['title' => __('home'), 'url' => BASE_URL],
            ['title' => __('categories'), 'url' => ''],
            ['title' => $category['name'], 'url' => '']
        ]);
        ?>
    </div>
</div>

<!-- Category Header -->
<section class="gradient-bg py-12">
    <div class="container mx-auto px-4 text-center text-white">
        <?php if ($category['icon']): ?>
        <i class="fas <?php echo htmlspecialchars($category['icon']); ?> text-5xl mb-4"></i>
        <?php endif; ?>
        <h1 class="text-3xl font-bold mb-2">
            <?php echo htmlspecialchars(isRTL() && $category['name_ar'] ? $category['name_ar'] : $category['name']); ?>
        </h1>
        <?php if ($category['description']): ?>
        <p class="text-gray-100 max-w-2xl mx-auto">
            <?php echo htmlspecialchars(isRTL() && $category['description_ar'] ? $category['description_ar'] : $category['description']); ?>
        </p>
        <?php endif; ?>
        <div class="mt-4 flex justify-center gap-6">
            <div>
                <span class="text-2xl font-bold"><?php echo $category['coupon_count']; ?></span>
                <span class="text-gray-200 block text-sm"><?php echo __('coupons'); ?></span>
            </div>
            <div>
                <span class="text-2xl font-bold"><?php echo $category['store_count']; ?></span>
                <span class="text-gray-200 block text-sm"><?php echo __('stores'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-2/3">
                <!-- Stores in Category -->
                <?php if (!empty($stores)): ?>
                <div class="mb-12">
                    <h2 class="text-xl font-bold mb-6"><?php echo __('stores'); ?> in <?php echo htmlspecialchars($category['name']); ?></h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <?php foreach (array_slice($stores, 0, 8) as $store): ?>
                        <a href="<?php echo BASE_URL; ?>/store/<?php echo $store['slug']; ?>" 
                           class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow hover:shadow-lg transition text-center">
                            <div class="w-14 h-14 mx-auto mb-2 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                <?php if ($store['logo']): ?>
                                <img src="<?php echo UPLOAD_URL . '/' . $store['logo']; ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                <span class="text-xl font-bold text-primary"><?php echo strtoupper(substr($store['name'], 0, 1)); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-medium text-sm truncate"><?php echo htmlspecialchars($store['name']); ?></h3>
                            <p class="text-xs text-gray-500"><?php echo $store['coupon_count']; ?> coupons</p>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Coupons -->
                <h2 class="text-xl font-bold mb-6"><?php echo htmlspecialchars($category['name']); ?> <?php echo __('coupons'); ?></h2>
                
                <?php if (!empty($coupons)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($coupons as $coupon): ?>
                    <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php echo paginationHtml($pagination, '/category/' . $category['slug']); ?>
                <?php else: ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center">
                    <i class="fas fa-ticket-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500"><?php echo __('no_coupons_found'); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="lg:w-1/3">
                <!-- Ad Slot -->
                <?php if (getSetting('ad_sidebar')): ?>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 mb-6">
                    <?php echo getSetting('ad_sidebar'); ?>
                </div>
                <?php endif; ?>
                
                <!-- All Categories -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
                    <h3 class="font-bold mb-4"><?php echo __('all_categories'); ?></h3>
                    <div class="space-y-2">
                        <?php 
                        $categoryModel = new Category();
                        $allCategories = $categoryModel->getAll();
                        foreach ($allCategories as $cat): 
                        ?>
                        <a href="<?php echo BASE_URL; ?>/category/<?php echo $cat['slug']; ?>" 
                           class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition <?php echo $cat['id'] == $category['id'] ? 'bg-primary/10 text-primary' : ''; ?>">
                            <span class="flex items-center">
                                <?php if ($cat['icon']): ?>
                                <i class="fas <?php echo htmlspecialchars($cat['icon']); ?> w-6"></i>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            </span>
                            <span class="text-sm text-gray-500"><?php echo $cat['coupon_count']; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
