<?php
/**
 * Admin - Add Category
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Add Category';

$categoryModel = new Category();
$parentCategories = $categoryModel->getParents();

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
    
    $data['slug'] = generateUniqueSlug('categories', $data['name']);
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadFile($_FILES['image'], 'uploads/categories');
        if ($upload['success']) {
            $data['image'] = $upload['path'];
        } else {
            $errors[] = 'Image upload failed: ' . $upload['error'];
        }
    }
    
    if (empty($errors)) {
        $categoryId = $categoryModel->create($data);
        if ($categoryId) {
            logActivity('category_create', 'Created category: ' . $data['name']);
            setFlash('success', 'Category created successfully.');
            redirect(ADMIN_URL . '/categories/');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Add Category</h1>
            <p class="text-gray-500">Create a new category</p>
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
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Category Information</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name (Arabic)</label>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome)</label>
                                <input type="text" name="icon" value="<?php echo htmlspecialchars($_POST['icon'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., fa-laptop">
                                <p class="text-xs text-gray-500 mt-1">Visit <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-500">fontawesome.com</a> for icons</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                                <input type="number" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? '0'); ?>"
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
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                            <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">None (Top Level)</option>
                                <?php foreach ($parentCategories as $parent): ?>
                                <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="w-full py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-save mr-2"></i>Save Category
                        </button>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Category Image</h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <label for="image" class="cursor-pointer">
                            <div id="image-preview" class="hidden mb-4">
                                <img src="" alt="Preview" class="w-24 h-24 mx-auto rounded-lg object-cover">
                            </div>
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload image</p>
                        </label>
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
