<?php
/**
 * Admin - Articles List
 */

require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/models/Article.php';

requireAuth();

$pageTitle = 'Manage Articles';

$articleModel = new Article();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (verifyCSRFToken($_GET['token'] ?? '')) {
        $articleModel->delete((int)$_GET['delete']);
        setFlash('success', 'Article deleted successfully.');
        redirect(ADMIN_URL . '/articles/');
    }
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$filters = ['status' => $_GET['status'] ?? null, 'search' => $_GET['search'] ?? null];
$articles = $articleModel->getAll($page, ADMIN_ITEMS_PER_PAGE, array_filter($filters));
$totalArticles = $articleModel->getCount(array_filter($filters));
$pagination = paginate($totalArticles, ADMIN_ITEMS_PER_PAGE, $page, '/admin/articles/');

include __DIR__ . '/../includes/header.php';
?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Articles</h1>
            <p class="text-gray-500">Manage blog articles</p>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/articles/add.php" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-plus mr-2"></i>Write Article
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" 
                       placeholder="Search articles..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="published" <?php echo ($filters['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="draft" <?php echo ($filters['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                <option value="scheduled" <?php echo ($filters['status'] ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Article</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (!empty($articles)): ?>
                        <?php foreach ($articles as $article): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <?php if ($article['cover_image']): ?>
                                    <img src="<?php echo UPLOAD_URL . '/' . $article['cover_image']; ?>" alt="" class="w-16 h-12 rounded object-cover mr-3">
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-medium text-gray-900 max-w-xs truncate"><?php echo htmlspecialchars($article['title']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo number_format($article['views_count']); ?> views</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($article['author_full_name'] ?? $article['author_name']); ?></td>
                            <td class="px-6 py-4 text-sm"><?php echo formatDate($article['publish_date'] ?? $article['created_at']); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php echo $article['status'] === 'published' ? 'bg-green-100 text-green-600' : 
                                               ($article['status'] === 'draft' ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600'); ?>">
                                    <?php echo ucfirst($article['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <?php if ($article['status'] === 'published'): ?>
                                    <a href="<?php echo BASE_URL; ?>/blog/<?php echo $article['slug']; ?>" target="_blank" class="p-2 text-gray-500 hover:text-blue-600"><i class="fas fa-eye"></i></a>
                                    <?php endif; ?>
                                    <a href="<?php echo ADMIN_URL; ?>/articles/edit.php?id=<?php echo $article['id']; ?>" class="p-2 text-gray-500 hover:text-green-600"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo ADMIN_URL; ?>/articles/?delete=<?php echo $article['id']; ?>&token=<?php echo generateCSRFToken(); ?>" 
                                       class="p-2 text-gray-500 hover:text-red-600" onclick="return confirm('Delete this article?');"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No articles found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
