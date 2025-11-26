<?php
/**
 * Admin - Add Store
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Store.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Add Store';

$storeModel = new Store();
$categoryModel = new Category();
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
        'status' => $_POST['status'] ?? 'active',
        'seo_title' => sanitize($_POST['seo_title'] ?? ''),
        'seo_description' => sanitize($_POST['seo_description'] ?? ''),
        'seo_keywords' => sanitize($_POST['seo_keywords'] ?? '')
    ];
    
    if (empty($data['name'])) {
        $errors[] = 'Store name is required';
    }
    
    $data['slug'] = generateUniqueSlug('stores', $data['name']);
    
    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $upload = uploadFile($_FILES['logo'], 'uploads/stores');
        if ($upload['success']) {
            $data['logo'] = $upload['path'];
        } else {
            $errors[] = 'Logo upload failed: ' . $upload['error'];
        }
    }
    
    if (empty($errors)) {
        $storeId = $storeModel->create($data);
        if ($storeId) {
            logActivity('store_create', 'Created store: ' . $data['name']);
            setFlash('success', 'Store created successfully.');
            redirect(ADMIN_URL . '/stores/');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Add Store</h1>
            <p class="text-gray-500">Create a new store</p>
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
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Store Information</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Name *</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Name (Arabic)</label>
                                <input type="text" name="name_ar" value="<?php echo htmlspecialchars($_POST['name_ar'] ?? ''); ?>" dir="rtl"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                                <input type="url" name="website" value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Affiliate URL</label>
                                <input type="url" name="affiliate_url" value="<?php echo htmlspecialchars($_POST['affiliate_url'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">SEO Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Title</label>
                            <input type="text" name="seo_title" value="<?php echo htmlspecialchars($_POST['seo_title'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Description</label>
                            <textarea name="seo_description" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($_POST['seo_description'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Keywords</label>
                            <input type="text" name="seo_keywords" value="<?php echo htmlspecialchars($_POST['seo_keywords'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Publish</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Featured Store</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_popular" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Popular Store</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-save mr-2"></i>Save Store
                        </button>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Store Logo</h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <label for="logo" class="cursor-pointer">
                            <div id="logo-preview" class="hidden mb-4">
                                <img src="" alt="Preview" class="w-24 h-24 mx-auto rounded-lg object-cover">
                            </div>
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Click to upload logo</p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
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
