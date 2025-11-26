<?php
/**
 * Article Controller
 * Handles blog/articles listing and individual articles
 */

require_once APP_PATH . '/controllers/BaseController.php';

class ArticleController extends BaseController {
    
    /**
     * List all articles
     */
    public function index() {
        $page = max(1, (int)$this->get('page', 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $totalArticles = $this->db->count('articles', "status = 'published'");
        $totalPages = ceil($totalArticles / $perPage);
        
        $articles = $this->db->fetchAll(
            "SELECT a.*, u.full_name as author_name, ac.name_en as category_name, ac.slug as category_slug
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             LEFT JOIN article_categories ac ON a.category_id = ac.id 
             WHERE a.status = 'published' 
             ORDER BY a.published_at DESC 
             LIMIT $perPage OFFSET $offset"
        );
        
        // Get featured articles
        $featuredArticles = $this->db->fetchAll(
            "SELECT a.*, u.full_name as author_name 
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             WHERE a.status = 'published' AND a.is_featured = 1 
             ORDER BY a.published_at DESC LIMIT 3"
        );
        
        // Get categories
        $categories = $this->db->fetchAll(
            "SELECT ac.*, (SELECT COUNT(*) FROM articles WHERE category_id = ac.id AND status = 'published') as articles_count 
             FROM article_categories ac 
             WHERE ac.is_active = 1 
             ORDER BY ac.order_position"
        );
        
        // Get popular tags
        $tags = $this->db->fetchAll(
            "SELECT t.*, COUNT(at.article_id) as usage_count 
             FROM tags t 
             JOIN article_tags at ON t.id = at.tag_id 
             GROUP BY t.id 
             ORDER BY usage_count DESC LIMIT 15"
        );
        
        $this->view('frontend/blog', [
            'pageTitle' => __('Blog', 'المدونة'),
            'articles' => $articles,
            'featuredArticles' => $featuredArticles,
            'categories' => $categories,
            'tags' => $tags,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles
        ]);
    }
    
    /**
     * Show single article
     */
    public function show($slug) {
        $article = $this->db->fetch(
            "SELECT a.*, u.full_name as author_name, u.avatar as author_avatar,
             ac.name_en as category_name, ac.slug as category_slug
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             LEFT JOIN article_categories ac ON a.category_id = ac.id 
             WHERE a.slug = :slug AND a.status = 'published'",
            ['slug' => $slug]
        );
        
        if (!$article) {
            http_response_code(404);
            $this->view('frontend/404');
            return;
        }
        
        // Increment view count
        $this->db->query("UPDATE articles SET views_count = views_count + 1 WHERE id = :id", ['id' => $article['id']]);
        
        // Get article tags
        $tags = $this->db->fetchAll(
            "SELECT t.* FROM tags t 
             JOIN article_tags at ON t.id = at.tag_id 
             WHERE at.article_id = :article_id",
            ['article_id' => $article['id']]
        );
        
        // Get related articles (same category)
        $relatedArticles = $this->db->fetchAll(
            "SELECT a.*, u.full_name as author_name 
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             WHERE a.category_id = :category_id AND a.id != :id AND a.status = 'published' 
             ORDER BY a.published_at DESC LIMIT 4",
            ['category_id' => $article['category_id'], 'id' => $article['id']]
        );
        
        // Get previous and next articles
        $prevArticle = $this->db->fetch(
            "SELECT id, title_en, title_ar, slug FROM articles 
             WHERE status = 'published' AND published_at < :published_at 
             ORDER BY published_at DESC LIMIT 1",
            ['published_at' => $article['published_at']]
        );
        
        $nextArticle = $this->db->fetch(
            "SELECT id, title_en, title_ar, slug FROM articles 
             WHERE status = 'published' AND published_at > :published_at 
             ORDER BY published_at ASC LIMIT 1",
            ['published_at' => $article['published_at']]
        );
        
        $this->view('frontend/article-single', [
            'pageTitle' => $article['seo_title'] ?: getLocalizedField($article, 'title'),
            'pageDescription' => $article['seo_description'] ?: getLocalizedField($article, 'excerpt'),
            'article' => $article,
            'tags' => $tags,
            'relatedArticles' => $relatedArticles,
            'prevArticle' => $prevArticle,
            'nextArticle' => $nextArticle
        ]);
    }
    
    /**
     * Show articles by category
     */
    public function category($slug) {
        $category = $this->db->fetch(
            "SELECT * FROM article_categories WHERE slug = :slug AND is_active = 1",
            ['slug' => $slug]
        );
        
        if (!$category) {
            http_response_code(404);
            $this->view('frontend/404');
            return;
        }
        
        $page = max(1, (int)$this->get('page', 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $totalArticles = $this->db->count('articles', "category_id = :category_id AND status = 'published'", ['category_id' => $category['id']]);
        $totalPages = ceil($totalArticles / $perPage);
        
        $articles = $this->db->fetchAll(
            "SELECT a.*, u.full_name as author_name 
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             WHERE a.category_id = :category_id AND a.status = 'published' 
             ORDER BY a.published_at DESC 
             LIMIT $perPage OFFSET $offset",
            ['category_id' => $category['id']]
        );
        
        $this->view('frontend/blog-category', [
            'pageTitle' => getLocalizedField($category, 'name') . ' - ' . __('Blog', 'المدونة'),
            'category' => $category,
            'articles' => $articles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles
        ]);
    }
    
    /**
     * Show articles by tag
     */
    public function tag($slug) {
        $tag = $this->db->fetch(
            "SELECT * FROM tags WHERE slug = :slug",
            ['slug' => $slug]
        );
        
        if (!$tag) {
            http_response_code(404);
            $this->view('frontend/404');
            return;
        }
        
        $page = max(1, (int)$this->get('page', 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $totalArticles = $this->db->fetch(
            "SELECT COUNT(*) as count FROM articles a 
             JOIN article_tags at ON a.id = at.article_id 
             WHERE at.tag_id = :tag_id AND a.status = 'published'",
            ['tag_id' => $tag['id']]
        )['count'];
        
        $totalPages = ceil($totalArticles / $perPage);
        
        $articles = $this->db->fetchAll(
            "SELECT a.*, u.full_name as author_name 
             FROM articles a 
             JOIN article_tags at ON a.id = at.article_id 
             LEFT JOIN users u ON a.author_id = u.id 
             WHERE at.tag_id = :tag_id AND a.status = 'published' 
             ORDER BY a.published_at DESC 
             LIMIT $perPage OFFSET $offset",
            ['tag_id' => $tag['id']]
        );
        
        $this->view('frontend/blog-tag', [
            'pageTitle' => getLocalizedField($tag, 'name') . ' - ' . __('Blog', 'المدونة'),
            'tag' => $tag,
            'articles' => $articles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles
        ]);
    }
}
