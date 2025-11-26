<?php
// Coupon Card Partial
// Used for displaying coupon cards across the site
?>
<div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow hover:shadow-lg transition group">
    <!-- Store Header -->
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                <?php if (!empty($coupon['store_logo'])): ?>
                <img src="<?php echo UPLOAD_URL . '/' . $coupon['store_logo']; ?>" alt="<?php echo htmlspecialchars($coupon['store_name']); ?>" class="w-full h-full object-cover">
                <?php else: ?>
                <span class="text-lg font-bold text-primary"><?php echo strtoupper(substr($coupon['store_name'] ?? 'S', 0, 1)); ?></span>
                <?php endif; ?>
            </div>
            <div>
                <a href="<?php echo BASE_URL; ?>/store/<?php echo $coupon['store_slug']; ?>" class="font-semibold hover:text-primary transition">
                    <?php echo htmlspecialchars($coupon['store_name'] ?? 'Store'); ?>
                </a>
                <?php if (!empty($coupon['is_verified'])): ?>
                <span class="inline-flex items-center text-xs text-green-600 dark:text-green-400">
                    <i class="fas fa-check-circle mr-1"></i><?php echo __('verified'); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Discount Badge -->
        <?php if ($coupon['discount_type'] !== 'other'): ?>
        <div class="bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold">
            <?php echo formatDiscount($coupon['discount_type'], $coupon['discount_value']); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Coupon Content -->
    <div class="p-4">
        <h3 class="font-semibold mb-2 line-clamp-2 group-hover:text-primary transition">
            <a href="<?php echo BASE_URL; ?>/coupon/<?php echo $coupon['slug']; ?>">
                <?php echo htmlspecialchars(isRTL() && !empty($coupon['title_ar']) ? $coupon['title_ar'] : $coupon['title']); ?>
            </a>
        </h3>
        
        <?php if (!empty($coupon['description'])): ?>
        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
            <?php echo htmlspecialchars(truncate(isRTL() && !empty($coupon['description_ar']) ? $coupon['description_ar'] : $coupon['description'], 100)); ?>
        </p>
        <?php endif; ?>
        
        <!-- Tags -->
        <div class="flex flex-wrap gap-2 mb-3">
            <?php if (!empty($coupon['is_exclusive'])): ?>
            <span class="inline-flex items-center px-2 py-1 text-xs bg-accent/10 text-accent rounded">
                <i class="fas fa-star mr-1"></i><?php echo __('exclusive'); ?>
            </span>
            <?php endif; ?>
            <?php if (!empty($coupon['is_featured'])): ?>
            <span class="inline-flex items-center px-2 py-1 text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded">
                <i class="fas fa-fire mr-1"></i><?php echo __('featured'); ?>
            </span>
            <?php endif; ?>
        </div>
        
        <!-- Expiry Info -->
        <?php
        $daysLeft = daysRemaining($coupon['expiry_date']);
        ?>
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            <?php if ($daysLeft === null): ?>
            <i class="fas fa-infinity mr-1"></i><?php echo __('no_expiry'); ?>
            <?php elseif ($daysLeft < 0): ?>
            <span class="text-red-500"><i class="fas fa-clock mr-1"></i><?php echo __('expired_on'); ?> <?php echo formatDate($coupon['expiry_date']); ?></span>
            <?php elseif ($daysLeft <= 3): ?>
            <span class="text-red-500"><i class="fas fa-clock mr-1"></i><?php echo $daysLeft; ?> <?php echo __('days_left'); ?>!</span>
            <?php else: ?>
            <i class="fas fa-clock mr-1"></i><?php echo __('expires'); ?> <?php echo formatDate($coupon['expiry_date']); ?>
            <?php endif; ?>
        </div>
        
        <!-- Action Button -->
        <?php if (!empty($coupon['code'])): ?>
        <div class="coupon-code-container relative">
            <button class="w-full py-3 bg-primary hover:bg-secondary text-white rounded-lg font-semibold transition coupon-reveal-btn"
                    data-coupon-id="<?php echo $coupon['id']; ?>"
                    data-code="<?php echo htmlspecialchars($coupon['code']); ?>"
                    data-affiliate="<?php echo htmlspecialchars($coupon['affiliate_url'] ?? ''); ?>">
                <span class="btn-text"><?php echo __('get_code'); ?></span>
                <span class="code-text hidden"><?php echo htmlspecialchars($coupon['code']); ?></span>
            </button>
            <div class="coupon-code-revealed hidden mt-2">
                <div class="flex items-center border-2 border-dashed border-primary rounded-lg overflow-hidden">
                    <span class="flex-1 py-3 px-4 bg-primary/5 font-mono font-bold text-primary text-center tracking-wider coupon-code-display">
                        <?php echo htmlspecialchars($coupon['code']); ?>
                    </span>
                    <button class="px-4 py-3 bg-primary text-white hover:bg-secondary transition copy-code-btn" data-code="<?php echo htmlspecialchars($coupon['code']); ?>">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-xs text-green-600 text-center mt-1 copy-success hidden">
                    <i class="fas fa-check mr-1"></i><?php echo __('copied'); ?>
                </p>
            </div>
        </div>
        <?php else: ?>
        <a href="<?php echo htmlspecialchars($coupon['affiliate_url'] ?? '#'); ?>" target="_blank" rel="nofollow noopener"
           class="block w-full py-3 bg-accent hover:bg-accent/90 text-white text-center rounded-lg font-semibold transition"
           onclick="trackCouponClick(<?php echo $coupon['id']; ?>)">
            <?php echo __('get_deal'); ?> <i class="fas fa-external-link-alt ml-1"></i>
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Usage Stats -->
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700/50 text-sm text-gray-500 dark:text-gray-400">
        <span><i class="fas fa-users mr-1"></i><?php echo number_format($coupon['used_count'] ?? 0); ?> <?php echo __('used_times'); ?></span>
        <span><i class="fas fa-chart-line mr-1"></i><?php echo $coupon['success_rate'] ?? 100; ?>% <?php echo __('success_rate'); ?></span>
    </div>
</div>

<script>
// Coupon reveal and copy functionality
document.addEventListener('DOMContentLoaded', function() {
    // Reveal coupon code
    document.querySelectorAll('.coupon-reveal-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('.coupon-code-container');
            const code = this.dataset.code;
            const affiliateUrl = this.dataset.affiliate;
            const couponId = this.dataset.couponId;
            
            // Track click
            if (couponId) {
                trackCouponClick(couponId);
            }
            
            // Show code
            this.classList.add('hidden');
            container.querySelector('.coupon-code-revealed').classList.remove('hidden');
            
            // Copy to clipboard
            navigator.clipboard.writeText(code).then(() => {
                container.querySelector('.copy-success').classList.remove('hidden');
                setTimeout(() => {
                    container.querySelector('.copy-success').classList.add('hidden');
                }, 3000);
            });
            
            // Open affiliate link
            if (affiliateUrl) {
                window.open(affiliateUrl, '_blank');
            }
        });
    });
    
    // Copy code button
    document.querySelectorAll('.copy-code-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            const container = this.closest('.coupon-code-container');
            
            navigator.clipboard.writeText(code).then(() => {
                container.querySelector('.copy-success').classList.remove('hidden');
                setTimeout(() => {
                    container.querySelector('.copy-success').classList.add('hidden');
                }, 3000);
            });
        });
    });
});

function trackCouponClick(couponId) {
    fetch('<?php echo BASE_URL; ?>/api/coupon/click/' + couponId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    }).catch(() => {});
}
</script>
