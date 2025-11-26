<?php
/**
 * Admin - Edit Coupon
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Coupon.php';
require_once APP_PATH . '/models/Store.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Edit Coupon';

$couponModel = new Coupon();
$storeModel = new Store();
$categoryModel = new Category();

// Get coupon ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$coupon = $couponModel->getById($id);

if (!$coupon) {
    setFlash('error', 'Coupon not found.');
    redirect(ADMIN_URL . '/coupons/');
}

$stores = $storeModel->getActive();
$categories = $categoryModel->getAll();

$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $data = [
        'title' => sanitize($_POST['title'] ?? ''),
        'title_ar' => sanitize($_POST['title_ar'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'description_ar' => sanitize($_POST['description_ar'] ?? ''),
        'code' => sanitize($_POST['code'] ?? ''),
        'discount_type' => $_POST['discount_type'] ?? 'percentage',
        'discount_value' => !empty($_POST['discount_value']) ? (float)$_POST['discount_value'] : null,
        'store_id' => (int)$_POST['store_id'],
        'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
        'affiliate_url' => sanitize($_POST['affiliate_url'] ?? ''),
        'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
        'expiry_date' => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_verified' => isset($_POST['is_verified']) ? 1 : 0,
        'is_exclusive' => isset($_POST['is_exclusive']) ? 1 : 0,
        'status' => $_POST['status'] ?? 'active',
        'seo_title' => sanitize($_POST['seo_title'] ?? ''),
        'seo_description' => sanitize($_POST['seo_description'] ?? '')
    ];
    
    // Validation
    if (empty($data['title'])) {
        $errors[] = 'Title is required';
    }
    if (empty($data['store_id'])) {
        $errors[] = 'Store is required';
    }
    
    // Update slug if title changed
    if ($data['title'] !== $coupon['title']) {
        $data['slug'] = generateUniqueSlug('coupons', $data['title'], $id);
    }
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadFile($_FILES['image'], 'uploads/coupons');
        if ($upload['success']) {
            // Delete old image
            if ($coupon['image']) {
                deleteFile($coupon['image']);
            }
            $data['image'] = $upload['path'];
        } else {
            $errors[] = 'Image upload failed: ' . $upload['error'];
        }
    }
    
    if (empty($errors)) {
        if ($couponModel->update($id, $data)) {
            logActivity('coupon_update', 'Updated coupon: ' . $data['title']);
            setFlash('success', 'Coupon updated successfully.');
            redirect(ADMIN_URL . '/coupons/');
        } else {
            $errors[] = 'Failed to update coupon. Please try again.';
        }
    }
    
    // Merge POST data for form repopulation
    $coupon = array_merge($coupon, $data);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Coupon</h1>
            <p class="text-gray-500">Update coupon details</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/coupons/" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Coupons
        </a>
    </div>
    
    <!-- Errors -->
    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <!-- Form -->
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrfField(); ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($coupon['title'] ?? ''); ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 20% Off Your First Order">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title (Arabic)</label>
                            <input type="text" name="title_ar" value="<?php echo htmlspecialchars($coupon['title_ar'] ?? ''); ?>" dir="rtl"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="العنوان بالعربية">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Describe the coupon details..."><?php echo htmlspecialchars($coupon['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (Arabic)</label>
                            <textarea name="description_ar" rows="3" dir="rtl"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="الوصف بالعربية..."><?php echo htmlspecialchars($coupon['description_ar'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Coupon Details -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Coupon Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code</label>
                            <input type="text" name="code" value="<?php echo htmlspecialchars($coupon['code'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono uppercase"
                                   placeholder="e.g., SAVE20">
                            <p class="text-xs text-gray-500 mt-1">Leave empty for deal-only offers</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                            <select name="discount_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="percentage" <?php echo ($coupon['discount_type'] ?? '') === 'percentage' ? 'selected' : ''; ?>>Percentage Off (%)</option>
                                <option value="fixed" <?php echo ($coupon['discount_type'] ?? '') === 'fixed' ? 'selected' : ''; ?>>Fixed Amount ($)</option>
                                <option value="free_shipping" <?php echo ($coupon['discount_type'] ?? '') === 'free_shipping' ? 'selected' : ''; ?>>Free Shipping</option>
                                <option value="other" <?php echo ($coupon['discount_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
                            <input type="number" name="discount_value" step="0.01" value="<?php echo htmlspecialchars($coupon['discount_value'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 20">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Affiliate URL</label>
                            <input type="url" name="affiliate_url" value="<?php echo htmlspecialchars($coupon['affiliate_url'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="https://example.com/ref=...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="<?php echo htmlspecialchars($coupon['start_date'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                            <input type="date" name="expiry_date" value="<?php echo htmlspecialchars($coupon['expiry_date'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                
                <!-- SEO -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">SEO Settings</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Title</label>
                            <input type="text" name="seo_title" value="<?php echo htmlspecialchars($coupon['seo_title'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Custom SEO title (optional)">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Description</label>
                            <textarea name="seo_description" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Custom meta description (optional)"><?php echo htmlspecialchars($coupon['seo_description'] ?? ''); ?></textarea>
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
                                <option value="active" <?php echo ($coupon['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="pending" <?php echo ($coupon['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="expired" <?php echo ($coupon['status'] ?? '') === 'expired' ? 'selected' : ''; ?>>Expired</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" <?php echo !empty($coupon['is_featured']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Featured Coupon</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_verified" <?php echo !empty($coupon['is_verified']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Verified</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_exclusive" <?php echo !empty($coupon['is_exclusive']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Exclusive</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Update Coupon
                        </button>
                    </div>
                </div>
                
                <!-- Store & Category -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Classification</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Store *</label>
                            <select name="store_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Store</option>
                                <?php foreach ($stores as $store): ?>
                                <option value="<?php echo $store['id']; ?>" <?php echo ($coupon['store_id'] ?? '') == $store['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($store['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($coupon['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Image -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Coupon Image</h2>
                    
                    <?php if (!empty($coupon['image'])): ?>
                    <div class="mb-4">
                        <img src="<?php echo UPLOAD_URL . '/' . $coupon['image']; ?>" alt="Current image" class="w-full h-32 object-cover rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Current image</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <label for="image" class="cursor-pointer">
                            <div id="image-preview" class="hidden mb-4">
                                <img src="" alt="Preview" class="max-w-full h-32 mx-auto rounded">
                            </div>
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Click to upload new image</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 5MB</p>
                        </label>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Statistics</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Views:</span>
                            <span class="font-medium"><?php echo number_format($coupon['views_count'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Used:</span>
                            <span class="font-medium"><?php echo number_format($coupon['used_count'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created:</span>
                            <span class="font-medium"><?php echo formatDate($coupon['created_at'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
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
