<?php
/**
 * Sitemap Generator
 * Generates XML sitemap for SEO
 */

require_once __DIR__ . '/config/config.php';
require_once APP_PATH . '/models/Coupon.php';
require_once APP_PATH . '/models/Store.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Article.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = BASE_URL;

// Initialize models
$couponModel = new Coupon();
$storeModel = new Store();
$categoryModel = new Category();
$articleModel = new Article();

// Get data
$coupons = $couponModel->getAll(1, 1000, ['status' => 'active']);
$stores = $storeModel->getAll(1, 500, ['status' => 'active']);
$categories = $categoryModel->getAll();
$articles = $articleModel->getPublished(1, 500);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc><?php echo $baseUrl; ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Static Pages -->
    <url>
        <loc><?php echo $baseUrl; ?>/stores</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/coupons</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/blog</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/expired</loc>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    
    <!-- Categories -->
    <?php foreach ($categories as $category): ?>
    <url>
        <loc><?php echo $baseUrl; ?>/category/<?php echo htmlspecialchars($category['slug']); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Stores -->
    <?php foreach ($stores as $store): ?>
    <url>
        <loc><?php echo $baseUrl; ?>/store/<?php echo htmlspecialchars($store['slug']); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($store['updated_at'])); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Coupons -->
    <?php foreach ($coupons as $coupon): ?>
    <url>
        <loc><?php echo $baseUrl; ?>/coupon/<?php echo htmlspecialchars($coupon['slug']); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($coupon['updated_at'])); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Articles -->
    <?php foreach ($articles as $article): ?>
    <url>
        <loc><?php echo $baseUrl; ?>/blog/<?php echo htmlspecialchars($article['slug']); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($article['updated_at'])); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
</urlset>
