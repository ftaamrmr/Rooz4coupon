    </main>
    
    <!-- Newsletter Section -->
    <section class="gradient-bg py-12 mt-12">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-3">
                <?= __('Get the Best Deals in Your Inbox', 'احصل على أفضل العروض في بريدك') ?>
            </h2>
            <p class="text-white/80 mb-6 max-w-2xl mx-auto">
                <?= __('Subscribe to our newsletter and never miss a deal. Get exclusive coupons directly to your inbox.', 'اشترك في نشرتنا الإخبارية ولا تفوت أي عرض. احصل على كوبونات حصرية مباشرة في بريدك.') ?>
            </p>
            <form action="<?= url('subscribe') ?>" method="POST" class="max-w-md mx-auto flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="email" name="email" required 
                       placeholder="<?= __('Enter your email', 'أدخل بريدك الإلكتروني') ?>" 
                       class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
                <button type="submit" class="px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                    <?= __('Subscribe', 'اشترك') ?>
                </button>
            </form>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4"><?= e(getSetting('site_name', 'Coupon & Deals')) ?></h3>
                    <p class="text-sm leading-relaxed">
                        <?= __('Your trusted source for the best coupons, promo codes, and deals from top brands and stores worldwide.', 'مصدرك الموثوق لأفضل الكوبونات وأكواد الخصم والعروض من أفضل العلامات التجارية والمتاجر حول العالم.') ?>
                    </p>
                    <!-- Social Links -->
                    <div class="flex gap-4 mt-4">
                        <?php if ($fb = getSetting('facebook_url')): ?>
                            <a href="<?= e($fb) ?>" target="_blank" class="hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if ($tw = getSetting('twitter_url')): ?>
                            <a href="<?= e($tw) ?>" target="_blank" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if ($ig = getSetting('instagram_url')): ?>
                            <a href="<?= e($ig) ?>" target="_blank" class="hover:text-white"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if ($yt = getSetting('youtube_url')): ?>
                            <a href="<?= e($yt) ?>" target="_blank" class="hover:text-white"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4"><?= __('Quick Links', 'روابط سريعة') ?></h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?= url('/') ?>" class="hover:text-white"><?= __('Home', 'الرئيسية') ?></a></li>
                        <li><a href="<?= url('stores') ?>" class="hover:text-white"><?= __('All Stores', 'جميع المتاجر') ?></a></li>
                        <li><a href="<?= url('categories') ?>" class="hover:text-white"><?= __('Categories', 'الفئات') ?></a></li>
                        <li><a href="<?= url('coupons') ?>" class="hover:text-white"><?= __('All Coupons', 'جميع الكوبونات') ?></a></li>
                        <li><a href="<?= url('expired-coupons') ?>" class="hover:text-white"><?= __('Expired Coupons', 'كوبونات منتهية') ?></a></li>
                        <li><a href="<?= url('blog') ?>" class="hover:text-white"><?= __('Blog', 'المدونة') ?></a></li>
                    </ul>
                </div>
                
                <!-- Popular Categories -->
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4"><?= __('Popular Categories', 'الفئات الشائعة') ?></h3>
                    <ul class="space-y-2 text-sm">
                        <?php
                        $footerCategories = db()->fetchAll("SELECT name_" . getCurrentLang() . " as name, slug FROM categories WHERE is_active = 1 ORDER BY order_position LIMIT 6");
                        foreach ($footerCategories as $cat):
                        ?>
                        <li><a href="<?= url('category/' . $cat['slug']) ?>" class="hover:text-white"><?= e($cat['name'] ?? $cat['slug']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4"><?= __('Contact Us', 'تواصل معنا') ?></h3>
                    <ul class="space-y-2 text-sm">
                        <?php if ($email = getSetting('site_email')): ?>
                            <li><i class="fas fa-envelope mr-2"></i> <?= e($email) ?></li>
                        <?php endif; ?>
                        <?php if ($phone = getSetting('site_phone')): ?>
                            <li><i class="fas fa-phone mr-2"></i> <?= e($phone) ?></li>
                        <?php endif; ?>
                        <?php if ($address = getSetting('site_address')): ?>
                            <li><i class="fas fa-map-marker-alt mr-2"></i> <?= e($address) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm"><?= getSetting('footer_text', '© ' . date('Y') . ' Coupon & Deals. All rights reserved.') ?></p>
                <div class="flex gap-4 text-sm">
                    <a href="<?= url('page/privacy-policy') ?>" class="hover:text-white"><?= __('Privacy Policy', 'سياسة الخصوصية') ?></a>
                    <a href="<?= url('page/terms') ?>" class="hover:text-white"><?= __('Terms of Service', 'شروط الخدمة') ?></a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Google AdSense Placeholder (uncomment when ready) -->
    <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script> -->
    
    <!-- JavaScript -->
    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        
        // Check for saved preference or system preference
        if (localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }
        
        darkModeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('darkMode', html.classList.contains('dark'));
        });
        
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Copy Coupon Code Function
        function copyCode(code, btn) {
            navigator.clipboard.writeText(code).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i> <?= __('Copied!', 'تم النسخ!') ?>';
                btn.classList.add('bg-green-500');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-500');
                }, 2000);
            });
        }
        
        // Countdown Timer
        function updateCountdowns() {
            document.querySelectorAll('[data-countdown]').forEach(el => {
                const expiry = new Date(el.dataset.countdown);
                const now = new Date();
                const diff = expiry - now;
                
                if (diff <= 0) {
                    el.textContent = '<?= __('Expired', 'منتهي') ?>';
                    el.classList.add('text-red-500');
                    return;
                }
                
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                
                if (days > 0) {
                    el.textContent = days + 'd ' + hours + 'h';
                } else {
                    el.textContent = hours + 'h ' + minutes + 'm';
                }
            });
        }
        
        updateCountdowns();
        setInterval(updateCountdowns, 60000);
    </script>
</body>
</html>
