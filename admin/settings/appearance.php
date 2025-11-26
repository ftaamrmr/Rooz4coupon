<?php
/**
 * Admin - Appearance Settings
 */

require_once __DIR__ . '/../../config/config.php';

requireRole(['admin']);

$pageTitle = 'Appearance Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $settings = [
        'primary_color' => sanitize($_POST['primary_color'] ?? '#3b82f6'),
        'secondary_color' => sanitize($_POST['secondary_color'] ?? '#1e40af'),
        'accent_color' => sanitize($_POST['accent_color'] ?? '#f59e0b'),
        'gradient_start' => sanitize($_POST['gradient_start'] ?? '#3b82f6'),
        'gradient_middle' => sanitize($_POST['gradient_middle'] ?? '#6366f1'),
        'gradient_end' => sanitize($_POST['gradient_end'] ?? '#8b5cf6'),
        'font_family' => sanitize($_POST['font_family'] ?? 'Inter'),
        'font_size_base' => (int)($_POST['font_size_base'] ?? 16),
        'hero_title' => sanitize($_POST['hero_title'] ?? ''),
        'hero_title_ar' => sanitize($_POST['hero_title_ar'] ?? ''),
        'hero_subtitle' => sanitize($_POST['hero_subtitle'] ?? ''),
        'hero_subtitle_ar' => sanitize($_POST['hero_subtitle_ar'] ?? ''),
        'footer_text' => sanitize($_POST['footer_text'] ?? ''),
        'footer_text_ar' => sanitize($_POST['footer_text_ar'] ?? '')
    ];
    
    foreach ($settings as $key => $value) {
        updateSetting($key, $value, 'appearance');
    }
    
    // Handle file uploads
    if (!empty($_FILES['logo']['name'])) {
        $upload = uploadFile($_FILES['logo'], 'uploads');
        if ($upload['success']) {
            updateSetting('logo', $upload['path'], 'appearance');
        }
    }
    
    if (!empty($_FILES['logo_dark']['name'])) {
        $upload = uploadFile($_FILES['logo_dark'], 'uploads');
        if ($upload['success']) {
            updateSetting('logo_dark', $upload['path'], 'appearance');
        }
    }
    
    if (!empty($_FILES['favicon']['name'])) {
        $upload = uploadFile($_FILES['favicon'], 'uploads');
        if ($upload['success']) {
            updateSetting('favicon', $upload['path'], 'appearance');
        }
    }
    
    if (!empty($_FILES['hero_bg_image']['name'])) {
        $upload = uploadFile($_FILES['hero_bg_image'], 'uploads');
        if ($upload['success']) {
            updateSetting('hero_bg_image', $upload['path'], 'appearance');
        }
    }
    
    logActivity('settings_update', 'Updated appearance settings');
    setFlash('success', 'Appearance settings saved successfully.');
    redirect(ADMIN_URL . '/settings/appearance.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Appearance Settings</h1>
        <p class="text-gray-500">Customize the look and feel of your website</p>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrfField(); ?>
        
        <!-- Logo & Branding -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Logo & Branding</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo (Light Mode)</label>
                    <?php if (getSetting('logo')): ?>
                    <div class="mb-2">
                        <img src="<?php echo UPLOAD_URL . '/' . getSetting('logo'); ?>" alt="Logo" class="h-12">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo (Dark Mode)</label>
                    <?php if (getSetting('logo_dark')): ?>
                    <div class="mb-2 bg-gray-800 p-2 rounded inline-block">
                        <img src="<?php echo UPLOAD_URL . '/' . getSetting('logo_dark'); ?>" alt="Logo Dark" class="h-12">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="logo_dark" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                    <?php if (getSetting('favicon')): ?>
                    <div class="mb-2">
                        <img src="<?php echo UPLOAD_URL . '/' . getSetting('favicon'); ?>" alt="Favicon" class="h-8">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="favicon" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>
        
        <!-- Colors -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Colors</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="primary_color" value="<?php echo getSetting('primary_color', '#3b82f6'); ?>" class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                        <input type="text" value="<?php echo getSetting('primary_color', '#3b82f6'); ?>" readonly class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="secondary_color" value="<?php echo getSetting('secondary_color', '#1e40af'); ?>" class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                        <input type="text" value="<?php echo getSetting('secondary_color', '#1e40af'); ?>" readonly class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="accent_color" value="<?php echo getSetting('accent_color', '#f59e0b'); ?>" class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                        <input type="text" value="<?php echo getSetting('accent_color', '#f59e0b'); ?>" readonly class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>
            
            <h3 class="text-md font-medium mt-6 mb-4">Gradient Colors (Header)</h3>
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start</label>
                    <input type="color" name="gradient_start" value="<?php echo getSetting('gradient_start', '#3b82f6'); ?>" class="w-full h-10 rounded border border-gray-300 cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle</label>
                    <input type="color" name="gradient_middle" value="<?php echo getSetting('gradient_middle', '#6366f1'); ?>" class="w-full h-10 rounded border border-gray-300 cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End</label>
                    <input type="color" name="gradient_end" value="<?php echo getSetting('gradient_end', '#8b5cf6'); ?>" class="w-full h-10 rounded border border-gray-300 cursor-pointer">
                </div>
            </div>
            
            <!-- Preview -->
            <div class="mt-4 p-4 rounded-lg text-white text-center font-bold" id="gradient-preview" style="background: linear-gradient(135deg, <?php echo getSetting('gradient_start', '#3b82f6'); ?>, <?php echo getSetting('gradient_middle', '#6366f1'); ?>, <?php echo getSetting('gradient_end', '#8b5cf6'); ?>);">
                Gradient Preview
            </div>
        </div>
        
        <!-- Hero Section -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Hero Section</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Title</label>
                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars(getSetting('hero_title', 'Find the Best Deals')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Title (Arabic)</label>
                    <input type="text" name="hero_title_ar" value="<?php echo htmlspecialchars(getSetting('hero_title_ar', '')); ?>" dir="rtl"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                    <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars(getSetting('hero_subtitle', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle (Arabic)</label>
                    <input type="text" name="hero_subtitle_ar" value="<?php echo htmlspecialchars(getSetting('hero_subtitle_ar', '')); ?>" dir="rtl"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Background Image</label>
                <?php if (getSetting('hero_bg_image')): ?>
                <div class="mb-2">
                    <img src="<?php echo UPLOAD_URL . '/' . getSetting('hero_bg_image'); ?>" alt="Hero BG" class="h-20 rounded">
                </div>
                <?php endif; ?>
                <input type="file" name="hero_bg_image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Footer</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                    <input type="text" name="footer_text" value="<?php echo htmlspecialchars(getSetting('footer_text', '')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text (Arabic)</label>
                    <input type="text" name="footer_text_ar" value="<?php echo htmlspecialchars(getSetting('footer_text_ar', '')); ?>" dir="rtl"
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

<script>
// Update gradient preview
document.querySelectorAll('input[name^="gradient"]').forEach(input => {
    input.addEventListener('input', function() {
        const start = document.querySelector('input[name="gradient_start"]').value;
        const middle = document.querySelector('input[name="gradient_middle"]').value;
        const end = document.querySelector('input[name="gradient_end"]').value;
        document.getElementById('gradient-preview').style.background = `linear-gradient(135deg, ${start}, ${middle}, ${end})`;
    });
});

// Sync color inputs
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    const textInput = colorInput.parentElement.querySelector('input[type="text"]');
    if (textInput) {
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
        });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
