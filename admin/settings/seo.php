<?php
/**
 * Admin - SEO Settings
 */

require_once __DIR__ . '/../../config/config.php';

requireRole(['admin']);

$pageTitle = 'SEO Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $settings = [
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? ''),
        'meta_keywords' => sanitize($_POST['meta_keywords'] ?? ''),
        'google_analytics' => sanitize($_POST['google_analytics'] ?? ''),
        'google_site_verification' => sanitize($_POST['google_site_verification'] ?? ''),
        'bing_site_verification' => sanitize($_POST['bing_site_verification'] ?? ''),
        'twitter_handle' => sanitize($_POST['twitter_handle'] ?? ''),
        'facebook_url' => sanitize($_POST['facebook_url'] ?? ''),
        'social_facebook' => sanitize($_POST['social_facebook'] ?? ''),
        'social_twitter' => sanitize($_POST['social_twitter'] ?? ''),
        'social_instagram' => sanitize($_POST['social_instagram'] ?? ''),
        'social_youtube' => sanitize($_POST['social_youtube'] ?? ''),
        'social_linkedin' => sanitize($_POST['social_linkedin'] ?? ''),
        'social_tiktok' => sanitize($_POST['social_tiktok'] ?? '')
    ];
    
    foreach ($settings as $key => $value) {
        updateSetting($key, $value, strpos($key, 'social_') === 0 ? 'social' : 'seo');
    }
    
    // Handle OG image upload
    if (!empty($_FILES['og_image']['name'])) {
        $upload = uploadFile($_FILES['og_image'], 'uploads');
        if ($upload['success']) {
            updateSetting('og_image', $upload['path'], 'seo');
        }
    }
    
    logActivity('settings_update', 'Updated SEO settings');
    setFlash('success', 'SEO settings saved successfully.');
    redirect(ADMIN_URL . '/settings/seo.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">SEO Settings</h1>
        <p class="text-gray-500">Optimize your website for search engines</p>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrfField(); ?>
        
        <!-- Meta Tags -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Global Meta Tags</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Meta Title</label>
                    <input type="text" name="meta_title" value="<?php echo htmlspecialchars(getSetting('meta_title', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Your Site Name - Best Coupons & Deals">
                    <p class="text-xs text-gray-500 mt-1">Recommended: 50-60 characters</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Meta Description</label>
                    <textarea name="meta_description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="A brief description of your website..."><?php echo htmlspecialchars(getSetting('meta_description', '')); ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Recommended: 150-160 characters</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="<?php echo htmlspecialchars(getSetting('meta_keywords', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="coupons, deals, promo codes, discounts">
                </div>
            </div>
        </div>
        
        <!-- Open Graph -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Open Graph (Social Sharing)</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Twitter Handle</label>
                    <input type="text" name="twitter_handle" value="<?php echo htmlspecialchars(getSetting('twitter_handle', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="@yourusername">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook Page URL</label>
                    <input type="url" name="facebook_url" value="<?php echo htmlspecialchars(getSetting('facebook_url', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="https://facebook.com/yourpage">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Default OG Image</label>
                <?php if (getSetting('og_image')): ?>
                <div class="mb-2">
                    <img src="<?php echo UPLOAD_URL . '/' . getSetting('og_image'); ?>" alt="OG Image" class="h-20 rounded">
                </div>
                <?php endif; ?>
                <input type="file" name="og_image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x630 pixels</p>
            </div>
        </div>
        
        <!-- Analytics & Verification -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Analytics & Verification</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
                    <input type="text" name="google_analytics" value="<?php echo htmlspecialchars(getSetting('google_analytics', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="G-XXXXXXXXXX or UA-XXXXXXXXX-X">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Site Verification</label>
                    <input type="text" name="google_site_verification" value="<?php echo htmlspecialchars(getSetting('google_site_verification', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Verification code">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bing Site Verification</label>
                    <input type="text" name="bing_site_verification" value="<?php echo htmlspecialchars(getSetting('bing_site_verification', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Verification code">
                </div>
            </div>
        </div>
        
        <!-- Social Media Links -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Social Media Links</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-facebook text-blue-600 mr-1"></i>Facebook</label>
                    <input type="url" name="social_facebook" value="<?php echo htmlspecialchars(getSetting('social_facebook', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-twitter text-sky-500 mr-1"></i>Twitter</label>
                    <input type="url" name="social_twitter" value="<?php echo htmlspecialchars(getSetting('social_twitter', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-instagram text-pink-600 mr-1"></i>Instagram</label>
                    <input type="url" name="social_instagram" value="<?php echo htmlspecialchars(getSetting('social_instagram', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-youtube text-red-600 mr-1"></i>YouTube</label>
                    <input type="url" name="social_youtube" value="<?php echo htmlspecialchars(getSetting('social_youtube', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-linkedin text-blue-700 mr-1"></i>LinkedIn</label>
                    <input type="url" name="social_linkedin" value="<?php echo htmlspecialchars(getSetting('social_linkedin', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-tiktok mr-1"></i>TikTok</label>
                    <input type="url" name="social_tiktok" value="<?php echo htmlspecialchars(getSetting('social_tiktok', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">SEO Tools</h2>
            <div class="flex flex-wrap gap-4">
                <a href="<?php echo BASE_URL; ?>/sitemap.xml" target="_blank" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    <i class="fas fa-sitemap mr-2"></i>View Sitemap
                </a>
                <a href="<?php echo BASE_URL; ?>/robots.txt" target="_blank" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    <i class="fas fa-robot mr-2"></i>View Robots.txt
                </a>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
