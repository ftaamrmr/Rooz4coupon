<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Page Header -->
<section class="bg-gray-200 dark:bg-gray-700 py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold mb-2"><?php echo __('expired'); ?> <?php echo __('coupons'); ?></h1>
        <p class="text-gray-600 dark:text-gray-300">These coupons have expired but may still work. Try them at your own risk.</p>
    </div>
</section>

<!-- Coupons Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <?php if (!empty($coupons)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($coupons as $coupon): ?>
            <div class="opacity-75 hover:opacity-100 transition">
                <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php echo paginationHtml($pagination, '/expired'); ?>
        <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
            <i class="fas fa-ticket-alt text-5xl text-gray-400 mb-4"></i>
            <p class="text-xl text-gray-500">No expired coupons found</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
