            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
    
    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50 transform -translate-x-full transition-transform lg:hidden">
        <div class="h-full flex flex-col">
            <div class="p-4 border-b flex items-center justify-between">
                <span class="text-xl font-bold text-gray-800"><?php echo getSetting('site_name', 'CouponHub'); ?></span>
                <button id="close-mobile-menu" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-home w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo ADMIN_URL; ?>/coupons/" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-ticket-alt w-5"></i>
                    <span>Coupons</span>
                </a>
                <a href="<?php echo ADMIN_URL; ?>/stores/" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-store w-5"></i>
                    <span>Stores</span>
                </a>
                <a href="<?php echo ADMIN_URL; ?>/categories/" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-folder w-5"></i>
                    <span>Categories</span>
                </a>
                <a href="<?php echo ADMIN_URL; ?>/articles/" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-newspaper w-5"></i>
                    <span>Articles</span>
                </a>
                <?php if (hasRole(['admin'])): ?>
                <hr class="my-4">
                <a href="<?php echo ADMIN_URL; ?>/settings/general.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-cog w-5"></i>
                    <span>Settings</span>
                </a>
                <a href="<?php echo ADMIN_URL; ?>/users/" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-users w-5"></i>
                    <span>Users</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    
    <script>
        // User dropdown toggle
        function toggleDropdown() {
            document.getElementById('dropdown-menu').classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('user-dropdown');
            if (!dropdown.contains(e.target)) {
                document.getElementById('dropdown-menu').classList.add('hidden');
            }
        });
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.remove('-translate-x-full');
            document.getElementById('mobile-sidebar-overlay').classList.remove('hidden');
        });
        
        document.getElementById('close-mobile-menu')?.addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.add('-translate-x-full');
            document.getElementById('mobile-sidebar-overlay').classList.add('hidden');
        });
        
        document.getElementById('mobile-sidebar-overlay')?.addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.add('-translate-x-full');
            this.classList.add('hidden');
        });
        
        // Initialize TinyMCE for rich text editors
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.rich-editor',
                height: 500,
                menubar: true,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'codesample'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic forecolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'link image media table codesample | removeformat | help',
                content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; }',
                images_upload_url: '<?php echo ADMIN_URL; ?>/api/upload.php',
                automatic_uploads: true,
                file_picker_types: 'image',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true
            });
        }
        
        // Auto-hide flash messages
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');
            flashMessages.forEach(function(msg) {
                msg.style.opacity = '0';
                msg.style.transition = 'opacity 0.5s';
                setTimeout(function() { msg.remove(); }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
