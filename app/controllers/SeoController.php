<?php
/**
 * SEO Controller
 * Handles sitemap.xml and robots.txt generation
 */

require_once APP_PATH . '/controllers/BaseController.php';

class SeoController extends BaseController {
    
    /**
     * Generate XML Sitemap
     */
    public function sitemap() {
        header('Content-Type: application/xml; charset=utf-8');
        
        $baseUrl = SITE_URL;
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        $this->sitemapUrl($baseUrl, date('Y-m-d'), '1.0', 'daily');
        
        // Static pages
        $this->sitemapUrl($baseUrl . '/stores', date('Y-m-d'), '0.9', 'daily');
        $this->sitemapUrl($baseUrl . '/categories', date('Y-m-d'), '0.9', 'weekly');
        $this->sitemapUrl($baseUrl . '/coupons', date('Y-m-d'), '0.9', 'daily');
        $this->sitemapUrl($baseUrl . '/blog', date('Y-m-d'), '0.8', 'daily');
        
        // Stores
        $stores = $this->db->fetchAll("SELECT slug, updated_at FROM stores WHERE is_active = 1");
        foreach ($stores as $store) {
            $this->sitemapUrl(
                $baseUrl . '/store/' . $store['slug'],
                date('Y-m-d', strtotime($store['updated_at'])),
                '0.8',
                'daily'
            );
        }
        
        // Categories
        $categories = $this->db->fetchAll("SELECT slug, updated_at FROM categories WHERE is_active = 1");
        foreach ($categories as $category) {
            $this->sitemapUrl(
                $baseUrl . '/category/' . $category['slug'],
                date('Y-m-d', strtotime($category['updated_at'])),
                '0.7',
                'weekly'
            );
        }
        
        // Articles
        $articles = $this->db->fetchAll("SELECT slug, updated_at FROM articles WHERE status = 'published'");
        foreach ($articles as $article) {
            $this->sitemapUrl(
                $baseUrl . '/blog/' . $article['slug'],
                date('Y-m-d', strtotime($article['updated_at'])),
                '0.7',
                'monthly'
            );
        }
        
        // Static pages
        $pages = $this->db->fetchAll("SELECT slug, updated_at FROM pages WHERE is_active = 1");
        foreach ($pages as $page) {
            $this->sitemapUrl(
                $baseUrl . '/page/' . $page['slug'],
                date('Y-m-d', strtotime($page['updated_at'])),
                '0.5',
                'monthly'
            );
        }
        
        echo '</urlset>';
        exit;
    }
    
    /**
     * Helper to output sitemap URL entry
     */
    private function sitemapUrl($loc, $lastmod, $priority, $changefreq) {
        echo '<url>' . "\n";
        echo '  <loc>' . htmlspecialchars($loc) . '</loc>' . "\n";
        echo '  <lastmod>' . $lastmod . '</lastmod>' . "\n";
        echo '  <changefreq>' . $changefreq . '</changefreq>' . "\n";
        echo '  <priority>' . $priority . '</priority>' . "\n";
        echo '</url>' . "\n";
    }
    
    /**
     * Generate robots.txt
     */
    public function robots() {
        header('Content-Type: text/plain');
        
        $baseUrl = SITE_URL;
        
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "\n";
        echo "# Disallow admin area\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /config/\n";
        echo "Disallow: /app/\n";
        echo "\n";
        echo "# Allow public assets\n";
        echo "Allow: /public/\n";
        echo "\n";
        echo "# Sitemap\n";
        echo "Sitemap: $baseUrl/sitemap.xml\n";
        
        exit;
    }
}
