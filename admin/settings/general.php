<?php
/**
 * Admin - General Settings
 */

require_once __DIR__ . '/../../config/config.php';

requireRole(['admin']);

$pageTitle = 'General Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $settings = [
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'site_name_ar' => sanitize($_POST['site_name_ar'] ?? ''),
        'site_tagline' => sanitize($_POST['site_tagline'] ?? ''),
        'site_tagline_ar' => sanitize($_POST['site_tagline_ar'] ?? ''),
        'site_email' => sanitize($_POST['site_email'] ?? ''),
        'site_phone' => sanitize($_POST['site_phone'] ?? ''),
        'default_language' => $_POST['default_language'] ?? 'en',
        'timezone' => $_POST['timezone'] ?? 'UTC',
        'date_format' => $_POST['date_format'] ?? 'Y-m-d',
        'session_timeout' => (int)($_POST['session_timeout'] ?? 30)
    ];
    
    foreach ($settings as $key => $value) {
        updateSetting($key, $value, 'general');
    }
    
    logActivity('settings_update', 'Updated general settings');
    setFlash('success', 'Settings saved successfully.');
    redirect(ADMIN_URL . '/settings/general.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">General Settings</h1>
        <p class="text-gray-500">Configure basic site settings</p>
    </div>
    
    <form method="POST" class="space-y-6">
        <?php echo csrfField(); ?>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Site Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars(getSetting('site_name', 'CouponHub')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Name (Arabic)</label>
                    <input type="text" name="site_name_ar" value="<?php echo htmlspecialchars(getSetting('site_name_ar', '')); ?>" dir="rtl"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Tagline</label>
                    <input type="text" name="site_tagline" value="<?php echo htmlspecialchars(getSetting('site_tagline', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Tagline (Arabic)</label>
                    <input type="text" name="site_tagline_ar" value="<?php echo htmlspecialchars(getSetting('site_tagline_ar', '')); ?>" dir="rtl"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="site_email" value="<?php echo htmlspecialchars(getSetting('site_email', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" name="site_phone" value="<?php echo htmlspecialchars(getSetting('site_phone', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Regional Settings</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Language</label>
                    <select name="default_language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="en" <?php echo getSetting('default_language', 'en') === 'en' ? 'selected' : ''; ?>>English</option>
                        <option value="ar" <?php echo getSetting('default_language', 'en') === 'ar' ? 'selected' : ''; ?>>Arabic</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                    <select name="timezone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="UTC" <?php echo getSetting('timezone', 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                        <option value="America/New_York" <?php echo getSetting('timezone') === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time (US)</option>
                        <option value="America/Los_Angeles" <?php echo getSetting('timezone') === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time (US)</option>
                        <option value="Europe/London" <?php echo getSetting('timezone') === 'Europe/London' ? 'selected' : ''; ?>>London</option>
                        <option value="Asia/Dubai" <?php echo getSetting('timezone') === 'Asia/Dubai' ? 'selected' : ''; ?>>Dubai</option>
                        <option value="Asia/Riyadh" <?php echo getSetting('timezone') === 'Asia/Riyadh' ? 'selected' : ''; ?>>Riyadh</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Format</label>
                    <select name="date_format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Y-m-d" <?php echo getSetting('date_format', 'Y-m-d') === 'Y-m-d' ? 'selected' : ''; ?>>2024-01-15</option>
                        <option value="d/m/Y" <?php echo getSetting('date_format') === 'd/m/Y' ? 'selected' : ''; ?>>15/01/2024</option>
                        <option value="m/d/Y" <?php echo getSetting('date_format') === 'm/d/Y' ? 'selected' : ''; ?>>01/15/2024</option>
                        <option value="M d, Y" <?php echo getSetting('date_format') === 'M d, Y' ? 'selected' : ''; ?>>Jan 15, 2024</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Timeout (minutes)</label>
                    <input type="number" name="session_timeout" value="<?php echo htmlspecialchars(getSetting('session_timeout', '30')); ?>" min="5" max="120"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
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
