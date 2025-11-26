<?php include APP_PATH . '/views/partials/header.php'; ?>

<section class="py-24">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-xl mx-auto">
            <div class="text-9xl font-bold text-primary mb-4">404</div>
            <h1 class="text-3xl font-bold mb-4"><?php echo __('page_not_found'); ?></h1>
            <p class="text-gray-600 dark:text-gray-300 mb-8">
                The page you are looking for doesn't exist or has been moved.
            </p>
            
            <!-- Search Box -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow mb-8">
                <p class="text-sm text-gray-500 mb-4">Try searching for what you need:</p>
                <form action="<?php echo BASE_URL; ?>/search" method="GET" class="flex gap-2">
                    <input type="text" name="q" placeholder="<?php echo __('search_placeholder'); ?>"
                           class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <!-- Quick Links -->
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?php echo BASE_URL; ?>" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <i class="fas fa-home mr-2"></i><?php echo __('home'); ?>
                </a>
                <a href="<?php echo BASE_URL; ?>/stores" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-store mr-2"></i><?php echo __('stores'); ?>
                </a>
                <a href="<?php echo BASE_URL; ?>/coupons" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-ticket-alt mr-2"></i><?php echo __('coupons'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
