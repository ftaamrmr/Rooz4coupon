    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-gray-950 text-gray-300 mt-16">
        <!-- Newsletter Section -->
        <div class="gradient-bg py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-2xl mx-auto text-center">
                    <h3 class="text-2xl font-bold text-white mb-4"><?php echo __('subscribe_newsletter'); ?></h3>
                    <p class="text-gray-100 mb-6">Get the latest deals and coupons delivered to your inbox</p>
                    <form id="newsletter-form" class="flex flex-col sm:flex-row gap-3 justify-center">
                        <?php echo csrfField(); ?>
                        <input type="email" name="email" placeholder="<?php echo __('email_placeholder'); ?>" required
                               class="px-4 py-3 rounded-lg flex-1 max-w-md bg-white/90 text-gray-800 focus:outline-none focus:ring-2 focus:ring-white">
                        <button type="submit" class="px-6 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-gray-100 transition">
                            <?php echo __('subscribe'); ?>
                        </button>
                    </form>
                    <p id="newsletter-message" class="mt-4 text-white hidden"></p>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h4 class="text-white text-lg font-semibold mb-4"><?php echo getSetting('site_name', 'CouponHub'); ?></h4>
                    <p class="text-gray-400 mb-4"><?php echo getSetting('site_tagline', 'Find the best coupons, deals, and promo codes from your favorite stores.'); ?></p>
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <?php if (getSetting('social_facebook')): ?>
                        <a href="<?php echo getSetting('social_facebook'); ?>" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (getSetting('social_twitter')): ?>
                        <a href="<?php echo getSetting('social_twitter'); ?>" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (getSetting('social_instagram')): ?>
                        <a href="<?php echo getSetting('social_instagram'); ?>" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (getSetting('social_youtube')): ?>
                        <a href="<?php echo getSetting('social_youtube'); ?>" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-white text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo BASE_URL; ?>/stores" class="text-gray-400 hover:text-white transition"><?php echo __('all_stores'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/coupons" class="text-gray-400 hover:text-white transition"><?php echo __('all_coupons'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/expired" class="text-gray-400 hover:text-white transition"><?php echo __('expired'); ?> <?php echo __('coupons'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/blog" class="text-gray-400 hover:text-white transition"><?php echo __('blog'); ?></a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="text-white text-lg font-semibold mb-4"><?php echo __('categories'); ?></h4>
                    <ul class="space-y-2">
                        <?php
                        $categoryModel = new Category();
                        $footerCategories = $categoryModel->getAll();
                        foreach (array_slice($footerCategories, 0, 6) as $cat):
                        ?>
                        <li><a href="<?php echo BASE_URL; ?>/category/<?php echo $cat['slug']; ?>" class="text-gray-400 hover:text-white transition"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-white text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo BASE_URL; ?>/about" class="text-gray-400 hover:text-white transition"><?php echo __('about_us'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/contact" class="text-gray-400 hover:text-white transition"><?php echo __('contact_us'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/privacy" class="text-gray-400 hover:text-white transition"><?php echo __('privacy_policy'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/terms" class="text-gray-400 hover:text-white transition"><?php echo __('terms_of_service'); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-gray-700">
            <div class="container mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        <?php echo isRTL() ? getSetting('footer_text_ar', getSetting('footer_text')) : getSetting('footer_text', '© ' . date('Y') . ' CouponHub. All rights reserved.'); ?>
                    </p>
                    <p class="text-gray-500 text-sm mt-2 md:mt-0">
                        Built with <i class="fas fa-heart text-red-500"></i> for savings
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Footer Ad Slot -->
    <?php if (getSetting('ad_footer')): ?>
    <div class="w-full bg-gray-100 dark:bg-gray-800 py-4">
        <div class="container mx-auto px-4 text-center">
            <?php echo getSetting('ad_footer'); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 p-3 bg-primary text-white rounded-full shadow-lg opacity-0 invisible transition-all duration-300 hover:bg-secondary">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript -->
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
    
    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        
        // Check saved theme
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }
        
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        });

        // Mobile Menu
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Search Overlay
        const searchToggle = document.getElementById('search-toggle');
        const searchOverlay = document.getElementById('search-overlay');
        const closeSearch = document.getElementById('close-search');
        const searchInput = document.getElementById('search-input');
        
        searchToggle.addEventListener('click', () => {
            searchOverlay.classList.remove('hidden');
            searchInput.focus();
        });
        
        closeSearch.addEventListener('click', () => {
            searchOverlay.classList.add('hidden');
        });
        
        searchOverlay.addEventListener('click', (e) => {
            if (e.target === searchOverlay) {
                searchOverlay.classList.add('hidden');
            }
        });

        // AJAX Search Suggestions
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                document.getElementById('search-suggestions').classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`<?php echo BASE_URL; ?>/api/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        const suggestions = document.getElementById('search-suggestions');
                        if (data.results && data.results.length > 0) {
                            let html = '<div class="space-y-2">';
                            data.results.forEach(item => {
                                html += `
                                    <a href="${item.url}" class="block p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                            ${item.image ? `<img src="${item.image}" alt="" class="w-10 h-10 rounded object-cover">` : '<div class="w-10 h-10 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center"><i class="fas fa-${item.type === 'store' ? 'store' : 'ticket-alt'}"></i></div>'}
                                            <div>
                                                <div class="font-medium">${item.title}</div>
                                                <div class="text-sm text-gray-500">${item.type === 'store' ? item.count : item.discount}</div>
                                            </div>
                                        </div>
                                    </a>
                                `;
                            });
                            html += '</div>';
                            suggestions.innerHTML = html;
                            suggestions.classList.remove('hidden');
                        } else {
                            suggestions.classList.add('hidden');
                        }
                    });
            }, 300);
        });

        // Back to Top
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });
        
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Newsletter Form
        const newsletterForm = document.getElementById('newsletter-form');
        const newsletterMessage = document.getElementById('newsletter-message');
        
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(newsletterForm);
            
            try {
                const response = await fetch('<?php echo BASE_URL; ?>/api/subscribe', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                newsletterMessage.textContent = data.message || data.error;
                newsletterMessage.classList.remove('hidden');
                
                if (data.success) {
                    newsletterForm.reset();
                }
            } catch (error) {
                newsletterMessage.textContent = 'An error occurred. Please try again.';
                newsletterMessage.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
