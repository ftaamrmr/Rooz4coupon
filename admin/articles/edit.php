<?php
/**
 * Admin - Edit Article
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Article.php';
require_once APP_PATH . '/models/Category.php';

requireAuth();

$pageTitle = 'Edit Article';

$articleModel = new Article();
$categoryModel = new Category();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = $articleModel->getById($id);

if (!$article) {
    setFlash('error', 'Article not found.');
    redirect(ADMIN_URL . '/articles/');
}

$categories = $categoryModel->getAll();
$tags = $articleModel->getTags($id);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $data = [
        'title' => sanitize($_POST['title'] ?? ''),
        'title_ar' => sanitize($_POST['title_ar'] ?? ''),
        'excerpt' => sanitize($_POST['excerpt'] ?? ''),
        'excerpt_ar' => sanitize($_POST['excerpt_ar'] ?? ''),
        'content' => cleanHtml($_POST['content'] ?? ''),
        'content_ar' => cleanHtml($_POST['content_ar'] ?? ''),
        'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
        'status' => $_POST['status'] ?? 'draft',
        'publish_date' => $_POST['status'] === 'scheduled' && !empty($_POST['publish_date']) 
                          ? $_POST['publish_date'] 
                          : ($article['publish_date'] ?? date('Y-m-d H:i:s')),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'seo_title' => sanitize($_POST['seo_title'] ?? ''),
        'seo_description' => sanitize($_POST['seo_description'] ?? ''),
        'seo_keywords' => sanitize($_POST['seo_keywords'] ?? '')
    ];
    
    if (empty($data['title'])) {
        $errors[] = 'Title is required';
    }
    if (empty($data['content'])) {
        $errors[] = 'Content is required';
    }
    
    // Update slug if title changed
    if ($data['title'] !== $article['title']) {
        $data['slug'] = generateUniqueSlug('articles', $data['title'], $id);
    }
    
    // Handle cover image upload
    if (!empty($_FILES['cover_image']['name'])) {
        $upload = uploadFile($_FILES['cover_image'], 'uploads/articles');
        if ($upload['success']) {
            if ($article['cover_image']) {
                deleteFile($article['cover_image']);
            }
            $data['cover_image'] = $upload['path'];
        } else {
            $errors[] = 'Cover image upload failed: ' . $upload['error'];
        }
    }
    
    if (empty($errors)) {
        if ($articleModel->update($id, $data)) {
            logActivity('article_update', 'Updated article: ' . $data['title']);
            setFlash('success', 'Article updated successfully.');
            redirect(ADMIN_URL . '/articles/');
        } else {
            $errors[] = 'Failed to update article. Please try again.';
        }
    }
    
    $article = array_merge($article, $data);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Article</h1>
            <p class="text-gray-500">Update article content</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/articles/" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Articles
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
                <!-- Title -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>" required
                                   class="w-full px-4 py-3 text-xl border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title (Arabic)</label>
                            <input type="text" name="title_ar" value="<?php echo htmlspecialchars($article['title_ar'] ?? ''); ?>" dir="rtl"
                                   class="w-full px-4 py-3 text-xl border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                
                <!-- Content Editor -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                    <textarea name="content" id="content" class="rich-editor w-full"><?php echo htmlspecialchars($article['content'] ?? ''); ?></textarea>
                </div>
                
                <!-- Arabic Content -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content (Arabic)</label>
                    <textarea name="content_ar" id="content_ar" class="rich-editor w-full" dir="rtl"><?php echo htmlspecialchars($article['content_ar'] ?? ''); ?></textarea>
                </div>
                
                <!-- Excerpt -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Excerpt</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                            <textarea name="excerpt" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($article['excerpt'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Short Description (Arabic)</label>
                            <textarea name="excerpt_ar" rows="3" dir="rtl"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($article['excerpt_ar'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- SEO -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">SEO Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Title</label>
                            <input type="text" name="seo_title" value="<?php echo htmlspecialchars($article['seo_title'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea name="seo_description" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($article['seo_description'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keywords</label>
                            <input type="text" name="seo_keywords" value="<?php echo htmlspecialchars($article['seo_keywords'] ?? ''); ?>"
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
                            <select name="status" id="status-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    onchange="toggleScheduleDate()">
                                <option value="draft" <?php echo ($article['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($article['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="scheduled" <?php echo ($article['status'] ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                            </select>
                        </div>
                        <div id="schedule-date" class="<?php echo ($article['status'] ?? '') !== 'scheduled' ? 'hidden' : ''; ?>">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                            <input type="datetime-local" name="publish_date" 
                                   value="<?php echo !empty($article['publish_date']) ? date('Y-m-d\TH:i', strtotime($article['publish_date'])) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" <?php echo !empty($article['is_featured']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Featured Article</span>
                        </label>
                        <button type="submit" class="w-full py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-save mr-2"></i>Update Article
                        </button>
                    </div>
                </div>
                
                <!-- Category -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Category</h2>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($article['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Tags -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Tags</h2>
                    <input type="text" name="tags" 
                           value="<?php echo htmlspecialchars(implode(', ', array_column($tags, 'name'))); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter tags separated by comma">
                </div>
                
                <!-- Cover Image -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Cover Image</h2>
                    
                    <?php if (!empty($article['cover_image'])): ?>
                    <div class="mb-4">
                        <img src="<?php echo UPLOAD_URL . '/' . $article['cover_image']; ?>" alt="Current cover" class="w-full h-40 object-cover rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Current cover image</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" name="cover_image" id="cover_image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <label for="cover_image" class="cursor-pointer">
                            <div id="cover-preview" class="hidden mb-4">
                                <img src="" alt="Preview" class="max-w-full h-40 mx-auto rounded-lg object-cover">
                            </div>
                            <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload new cover image</p>
                        </label>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Statistics</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Views:</span>
                            <span class="font-medium"><?php echo number_format($article['views_count'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Author:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($article['author_full_name'] ?? $article['author_name'] ?? 'Unknown'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created:</span>
                            <span class="font-medium"><?php echo formatDate($article['created_at'] ?? ''); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Updated:</span>
                            <span class="font-medium"><?php echo formatDate($article['updated_at'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleScheduleDate() {
    const status = document.getElementById('status-select').value;
    const scheduleDate = document.getElementById('schedule-date');
    if (status === 'scheduled') {
        scheduleDate.classList.remove('hidden');
    } else {
        scheduleDate.classList.add('hidden');
    }
}

function previewImage(input) {
    const preview = document.getElementById('cover-preview');
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
