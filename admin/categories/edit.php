<?php
/**
 * Admin - Edit Category
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Edit Category';

$categoryModel = new Category();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = $categoryModel->getById($id);

if (!$category) {
    setFlash('error', 'Category not found.');
    redirect(ADMIN_URL . '/categories/');
}

$allCategories = $categoryModel->getAll(true);
$parentCategories = array_filter($allCategories, function($cat) use ($id) {
    return $cat['id'] != $id;
});

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'name_ar' => sanitize($_POST['name_ar'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'description_ar' => sanitize($_POST['description_ar'] ?? ''),
        'icon' => sanitize($_POST['icon'] ?? ''),
        'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
        'seo_title' => sanitize($_POST['seo_title'] ?? ''),
        'seo_description' => sanitize($_POST['seo_description'] ?? '')
    ];
    
    if (empty($data['name'])) {
        $errors[] = 'Category name is required';
    }
    
    // Prevent self-referencing parent
    if ($data['parent_id'] == $id) {
        $errors[] = 'Category cannot be its own parent';
    }
    
    // Update slug if name changed
    if ($data['name'] !== $category['name']) {
        $data['slug'] = generateUniqueSlug('categories', $data['name'], $id);
    }
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadFile($_FILES['image'], 'uploads/categories');
        if ($upload['success']) {
            if ($category['image']) {
                deleteFile($category['image']);
            }
            $data['image'] = $upload['path'];
        } else {
            $errors[] = 'Image upload failed: ' . $upload['error'];
        }
    }
    
    if (empty($errors)) {
        if ($categoryModel->update($id, $data)) {
            logActivity('category_update', 'Updated category: ' . $data['name']);
            setFlash('success', 'Category updated successfully.');
            redirect(ADMIN_URL . '/categories/');
        } else {
            $errors[] = 'Failed to update category. Please try again.';
        }
    }
    
    $category = array_merge($category, $data);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Category</h1>
            <p class="text-gray-500">Update category details</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/categories/" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Categories
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category Name (Arabic)</label>
                                <input type="text" name="name_ar" value="<?php echo htmlspecialchars($category['name_ar'] ?? ''); ?>" dir="rtl"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (Arabic)</label>
                            <textarea name="description_ar" rows="3" dir="rtl"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($category['description_ar'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome class)</label>
                            <input type="text" name="icon" value="<?php echo htmlspecialchars($category['icon'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., fa-laptop, fa-tshirt">
                            <p class="text-xs text-gray-500 mt-1">Browse icons at <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-600">fontawesome.com</a></p>
                        </div>
                    </div>
                </div>
                
                <!-- SEO -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">SEO Settings</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Title</label>
                            <input type="text" name="seo_title" value="<?php echo htmlspecialchars($category['seo_title'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea name="seo_description" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($category['seo_description'] ?? ''); ?></textarea>
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
                                <option value="active" <?php echo ($category['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($category['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                            <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">None (Top Level)</option>
                                <?php foreach ($parentCategories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($category['parent_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="<?php echo htmlspecialchars($category['sort_order'] ?? 0); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Update Category
                        </button>
                    </div>
                </div>
                
                <!-- Image -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Category Image</h2>
                    
                    <?php if (!empty($category['image'])): ?>
                    <div class="mb-4 text-center">
                        <img src="<?php echo UPLOAD_URL . '/' . $category['image']; ?>" alt="Category image" class="w-full h-32 object-cover rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Current image</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <label for="image" class="cursor-pointer">
                            <div id="image-preview" class="hidden mb-4">
                                <img src="" alt="Preview" class="w-full h-32 mx-auto rounded-lg object-cover">
                            </div>
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload new image</p>
                        </label>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Statistics</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Coupons:</span>
                            <span class="font-medium"><?php echo number_format($category['coupon_count'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Stores:</span>
                            <span class="font-medium"><?php echo number_format($category['store_count'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created:</span>
                            <span class="font-medium"><?php echo formatDate($category['created_at'] ?? ''); ?></span>
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
