<?php include APP_PATH . '/views/partials/header.php'; ?>

<!-- Coupon Schema.org Markup -->
<?php
echo schemaJsonLd('Offer', [
    'name' => $coupon['title'],
    'description' => $coupon['description'] ?? '',
    'url' => BASE_URL . '/coupon/' . $coupon['slug'],
    'priceCurrency' => 'USD',
    'availability' => $coupon['status'] === 'active' ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
    'validFrom' => $coupon['start_date'] ?? $coupon['created_at'],
    'validThrough' => $coupon['expiry_date'] ?? '',
    'seller' => [
        '@type' => 'Organization',
        'name' => $coupon['store_name']
    ]
]);
?>

<!-- Breadcrumbs -->
<div class="bg-gray-100 dark:bg-gray-800 py-4">
    <div class="container mx-auto px-4">
        <?php
        echo breadcrumbs([
            ['title' => __('home'), 'url' => BASE_URL],
            ['title' => $coupon['store_name'], 'url' => BASE_URL . '/store/' . $coupon['store_slug']],
            ['title' => truncate($coupon['title'], 50), 'url' => '']
        ]);
        ?>
    </div>
</div>

<!-- Coupon Detail -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-2/3">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="gradient-bg p-6 text-white">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <div class="w-20 h-20 rounded-xl bg-white flex items-center justify-center overflow-hidden">
                                    <?php if ($coupon['store_logo']): ?>
                                    <img src="<?php echo UPLOAD_URL . '/' . $coupon['store_logo']; ?>" alt="<?php echo htmlspecialchars($coupon['store_name']); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <span class="text-3xl font-bold text-primary"><?php echo strtoupper(substr($coupon['store_name'], 0, 1)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="<?php echo BASE_URL; ?>/store/<?php echo $coupon['store_slug']; ?>" class="text-xl font-bold hover:underline">
                                        <?php echo htmlspecialchars($coupon['store_name']); ?>
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <?php if ($coupon['is_verified']): ?>
                                        <span class="inline-flex items-center px-2 py-1 bg-green-500/20 rounded text-sm">
                                            <i class="fas fa-check-circle mr-1"></i><?php echo __('verified'); ?>
                                        </span>
                                        <?php endif; ?>
                                        <?php if ($coupon['is_exclusive']): ?>
                                        <span class="inline-flex items-center px-2 py-1 bg-yellow-500/20 rounded text-sm">
                                            <i class="fas fa-star mr-1"></i><?php echo __('exclusive'); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Discount Badge -->
                            <?php if ($coupon['discount_type'] !== 'other'): ?>
                            <div class="text-right">
                                <div class="text-4xl font-bold">
                                    <?php 
                                    if ($coupon['discount_type'] === 'percentage') {
                                        echo $coupon['discount_value'] . '%';
                                    } elseif ($coupon['discount_type'] === 'fixed') {
                                        echo '$' . number_format($coupon['discount_value'], 0);
                                    } else {
                                        echo 'FREE';
                                    }
                                    ?>
                                </div>
                                <div class="text-sm opacity-75">
                                    <?php 
                                    if ($coupon['discount_type'] === 'percentage' || $coupon['discount_type'] === 'fixed') {
                                        echo 'OFF';
                                    } else {
                                        echo 'SHIPPING';
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <h1 class="text-2xl font-bold mb-4">
                            <?php echo htmlspecialchars(isRTL() && $coupon['title_ar'] ? $coupon['title_ar'] : $coupon['title']); ?>
                        </h1>
                        
                        <?php if ($coupon['description']): ?>
                        <div class="text-gray-600 dark:text-gray-300 mb-6">
                            <?php echo nl2br(htmlspecialchars(isRTL() && $coupon['description_ar'] ? $coupon['description_ar'] : $coupon['description'])); ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Stats -->
                        <div class="flex flex-wrap gap-6 mb-6 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center">
                                <i class="fas fa-users mr-2 text-primary"></i>
                                <span><?php echo number_format($coupon['used_count']); ?> <?php echo __('used_times'); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chart-line mr-2 text-green-500"></i>
                                <span><?php echo $coupon['success_rate']; ?>% <?php echo __('success_rate'); ?></span>
                            </div>
                            <?php
                            $daysLeft = daysRemaining($coupon['expiry_date']);
                            ?>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2 <?php echo $daysLeft !== null && $daysLeft <= 3 ? 'text-red-500' : 'text-gray-400'; ?>"></i>
                                <?php if ($daysLeft === null): ?>
                                <span><?php echo __('no_expiry'); ?></span>
                                <?php elseif ($daysLeft < 0): ?>
                                <span class="text-red-500"><?php echo __('expired_on'); ?> <?php echo formatDate($coupon['expiry_date']); ?></span>
                                <?php else: ?>
                                <span class="<?php echo $daysLeft <= 3 ? 'text-red-500 font-semibold' : ''; ?>">
                                    <?php echo $daysLeft; ?> <?php echo __('days_left'); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Countdown Timer -->
                        <?php if ($coupon['expiry_date'] && $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7): ?>
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 mb-6">
                            <div class="text-center">
                                <p class="text-red-600 dark:text-red-400 font-semibold mb-2">⏰ Hurry! Offer ends soon</p>
                                <div id="countdown" class="flex justify-center gap-4" data-expiry="<?php echo $coupon['expiry_date']; ?>">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="days">0</div>
                                        <div class="text-xs text-gray-500">Days</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="hours">0</div>
                                        <div class="text-xs text-gray-500">Hours</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="minutes">0</div>
                                        <div class="text-xs text-gray-500">Minutes</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="seconds">0</div>
                                        <div class="text-xs text-gray-500">Seconds</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                        (function() {
                            const countdown = document.getElementById('countdown');
                            const expiry = new Date(countdown.dataset.expiry + 'T23:59:59');
                            
                            function updateCountdown() {
                                const now = new Date();
                                const diff = expiry - now;
                                
                                if (diff <= 0) {
                                    document.getElementById('days').textContent = '0';
                                    document.getElementById('hours').textContent = '0';
                                    document.getElementById('minutes').textContent = '0';
                                    document.getElementById('seconds').textContent = '0';
                                    return;
                                }
                                
                                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                
                                document.getElementById('days').textContent = days;
                                document.getElementById('hours').textContent = hours;
                                document.getElementById('minutes').textContent = minutes;
                                document.getElementById('seconds').textContent = seconds;
                            }
                            
                            updateCountdown();
                            setInterval(updateCountdown, 1000);
                        })();
                        </script>
                        <?php endif; ?>
                        
                        <!-- Coupon Code Section -->
                        <?php if ($coupon['code']): ?>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Coupon Code:</p>
                            <div class="flex items-center border-2 border-dashed border-primary rounded-lg overflow-hidden">
                                <span class="flex-1 py-4 px-6 bg-primary/5 font-mono font-bold text-xl text-primary text-center tracking-wider">
                                    <?php echo htmlspecialchars($coupon['code']); ?>
                                </span>
                                <button id="copy-code-btn" class="px-6 py-4 bg-primary text-white hover:bg-secondary transition" data-code="<?php echo htmlspecialchars($coupon['code']); ?>">
                                    <i class="fas fa-copy mr-2"></i><?php echo __('copy_code'); ?>
                                </button>
                            </div>
                            <p id="copy-success" class="text-green-600 text-center mt-2 hidden">
                                <i class="fas fa-check mr-1"></i><?php echo __('copied'); ?>
                            </p>
                            
                            <?php if ($coupon['affiliate_url']): ?>
                            <a href="<?php echo htmlspecialchars($coupon['affiliate_url']); ?>" target="_blank" rel="nofollow noopener"
                               onclick="trackCouponClick(<?php echo $coupon['id']; ?>)"
                               class="block w-full mt-4 py-3 bg-accent hover:bg-accent/90 text-white text-center rounded-lg font-semibold transition">
                                <?php echo __('visit_store'); ?> <i class="fas fa-external-link-alt ml-2"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <a href="<?php echo htmlspecialchars($coupon['affiliate_url'] ?? '#'); ?>" target="_blank" rel="nofollow noopener"
                           onclick="trackCouponClick(<?php echo $coupon['id']; ?>)"
                           class="block w-full py-4 bg-primary hover:bg-secondary text-white text-center rounded-xl text-lg font-semibold transition">
                            <?php echo __('get_deal'); ?> <i class="fas fa-external-link-alt ml-2"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Related Coupons -->
                <?php if (!empty($relatedCoupons)): ?>
                <div class="mt-12">
                    <h2 class="text-xl font-bold mb-6">Related Coupons</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($relatedCoupons as $coupon): ?>
                        <?php include APP_PATH . '/views/partials/coupon-card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="lg:w-1/3">
                <!-- Ad Slot -->
                <?php if (getSetting('ad_coupon_sidebar')): ?>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 mb-6">
                    <?php echo getSetting('ad_coupon_sidebar'); ?>
                </div>
                <?php endif; ?>
                
                <!-- Store Info Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow mb-6">
                    <h3 class="font-bold mb-4"><?php echo __('about_store'); ?></h3>
                    
                    <div class="flex items-center space-x-3 rtl:space-x-reverse mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                            <?php if ($coupon['store_logo']): ?>
                            <img src="<?php echo UPLOAD_URL . '/' . $coupon['store_logo']; ?>" alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                            <span class="font-bold text-primary"><?php echo strtoupper(substr($coupon['store_name'], 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="<?php echo BASE_URL; ?>/store/<?php echo $coupon['store_slug']; ?>" class="font-semibold hover:text-primary transition">
                                <?php echo htmlspecialchars($coupon['store_name']); ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($coupon['store_description']): ?>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        <?php echo htmlspecialchars(truncate($coupon['store_description'], 150)); ?>
                    </p>
                    <?php endif; ?>
                    
                    <a href="<?php echo BASE_URL; ?>/store/<?php echo $coupon['store_slug']; ?>" class="block w-full py-2 text-center border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition">
                        View All <?php echo htmlspecialchars($coupon['store_name']); ?> Coupons
                    </a>
                </div>
                
                <!-- Share Buttons -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
                    <h3 class="font-bold mb-4"><?php echo __('share'); ?> This Deal</h3>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(currentUrl()); ?>" target="_blank" rel="noopener"
                           class="flex-1 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(currentUrl()); ?>&text=<?php echo urlencode($coupon['title']); ?>" target="_blank" rel="noopener"
                           class="flex-1 py-2 bg-sky-500 text-white text-center rounded-lg hover:bg-sky-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($coupon['title'] . ' ' . currentUrl()); ?>" target="_blank" rel="noopener"
                           class="flex-1 py-2 bg-green-500 text-white text-center rounded-lg hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="mailto:?subject=<?php echo urlencode($coupon['title']); ?>&body=<?php echo urlencode(currentUrl()); ?>"
                           class="flex-1 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<script>
document.getElementById('copy-code-btn').addEventListener('click', function() {
    const code = this.dataset.code;
    navigator.clipboard.writeText(code).then(() => {
        document.getElementById('copy-success').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('copy-success').classList.add('hidden');
        }, 3000);
    });
});

function trackCouponClick(couponId) {
    fetch('<?php echo BASE_URL; ?>/api/coupon/click/' + couponId, {
        method: 'POST'
    }).catch(() => {});
}
</script>

<?php include APP_PATH . '/views/partials/footer.php'; ?>
