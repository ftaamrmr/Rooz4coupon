<?php
/**
 * Admin - Edit Store
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Store.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Edit Store';

$storeModel = new Store();
$categoryModel = new Category();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$store = $storeModel->getById($id);

if (!$store) {
    setFlash('error', 'Store not found.');
    redirect(ADMIN_URL . '/stores/');
}

$categories = $categoryModel->getAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'name_ar' => sanitize($_POST['name_ar'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'description_ar' => sanitize($_POST['description_ar'] ?? ''),
        'website' => sanitize($_POST['website'] ?? ''),
        'affiliate_url' => sanitize($_POST['affiliate_url'] ?? ''),
        'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
        'seo_title' => sanitize($_POST['seo_title'] ?? ''),
        'seo_description' => sanitize($_POST['seo_description'] ?? ''),
        'seo_keywords' => sanitize($_POST['seo_keywords'] ?? '')
    ];
    
    if (empty($data['name'])) {
        $errors[] = 'Store name is required';
    }
    
    // Update slug if name changed
    if ($data['name'] !== $store['name']) {
        $data['slug'] = generateUniqueSlug('stores', $data['name'], $id);
    }
    
    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $upload = uploadFile($_FILES['logo'], 'uploads/stores');
        if ($upload['success']) {
            if ($store['logo']) {
                deleteFile($store['logo']);
            }
            $data['logo'] = $upload['path'];
        } else {
            $errors[] = 'Logo upload failed: ' . $upload['error'];
        }
    }
    
    if (empty($errors)) {
        if ($storeModel->update($id, $data)) {
            logActivity('store_update', 'Updated store: ' . $data['name']);
            setFlash('success', 'Store updated successfully.');
            redirect(ADMIN_URL . '/stores/');
        } else {
            $errors[] = 'Failed to update store. Please try again.';
        }
    }
    
    $store = array_merge($store, $data);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Store</h1>
            <p class="text-gray-500">Update store details</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/stores/" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Stores
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrfField(); ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Name *</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($store['name'] ?? ''); ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Name (Arabic)</label>
                                <input type="text" name="name_ar" value="<?php echo htmlspecialchars($store['name_ar'] ?? ''); ?>" dir="rtl"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($store['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (Arabic)</label>
                            <textarea name="description_ar" rows="3" dir="rtl"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($store['description_ar'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                                <input type="url" name="website" value="<?php echo htmlspecialchars($store['website'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Affiliate URL</label>
                                <input type="url" name="affiliate_url" value="<?php echo htmlspecialchars($store['affiliate_url'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- SEO -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">SEO Settings</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Title</label>
                            <input type="text" name="seo_title" value="<?php echo htmlspecialchars($store['seo_title'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea name="seo_description" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($store['seo_description'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keywords</label>
                            <input type="text" name="seo_keywords" value="<?php echo htmlspecialchars($store['seo_keywords'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Publish</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active" <?php echo ($store['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($store['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" <?php echo !empty($store['is_featured']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Featured Store</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_popular" <?php echo !empty($store['is_popular']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Popular Store</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="<?php echo htmlspecialchars($store['sort_order'] ?? 0); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <button type="submit" class="w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-save mr-2"></i>Update Store
                        </button>
                    </div>
                </div>
                
                <!-- Category -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Category</h2>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($store['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Logo -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Store Logo</h2>
                    
                    <?php if (!empty($store['logo'])): ?>
                    <div class="mb-4 text-center">
                        <img src="<?php echo UPLOAD_URL . '/' . $store['logo']; ?>" alt="Current logo" class="w-24 h-24 mx-auto rounded-lg object-cover">
                        <p class="text-xs text-gray-500 mt-1">Current logo</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="previewLogo(this)">
                        <label for="logo" class="cursor-pointer">
                            <div id="logo-preview" class="hidden mb-4">
                                <img src="" alt="Preview" class="w-24 h-24 mx-auto rounded-lg object-cover">
                            </div>
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload new logo</p>
                        </label>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Statistics</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Views:</span>
                            <span class="font-medium"><?php echo number_format($store['views_count'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Coupons:</span>
                            <span class="font-medium"><?php echo number_format($store['coupon_count'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created:</span>
                            <span class="font-medium"><?php echo formatDate($store['created_at'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewLogo(input) {
    const preview = document.getElementById('logo-preview');
    const img = preview.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
