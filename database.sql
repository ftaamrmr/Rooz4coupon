-- =====================================================
-- Coupons & Deals Website Database Schema
-- Compatible with MySQL 5.7+ / MariaDB 10.3+
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `coupon_site` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `coupon_site`;

-- =====================================================
-- Users Table (Admin, Editor, Writer roles)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) DEFAULT NULL,
    `role` ENUM('admin', 'editor', 'writer') DEFAULT 'writer',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_role` (`role`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Categories Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_en` VARCHAR(100) NOT NULL,
    `name_ar` VARCHAR(100) DEFAULT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description_en` TEXT DEFAULT NULL,
    `description_ar` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `parent_id` INT(11) UNSIGNED DEFAULT NULL,
    `order_position` INT(11) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_order` (`order_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Stores Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `stores` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `logo` VARCHAR(255) DEFAULT NULL,
    `description_en` TEXT DEFAULT NULL,
    `description_ar` TEXT DEFAULT NULL,
    `website_url` VARCHAR(255) DEFAULT NULL,
    `affiliate_url` VARCHAR(500) DEFAULT NULL,
    `category_id` INT(11) UNSIGNED DEFAULT NULL,
    `seo_title` VARCHAR(255) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `seo_keywords` VARCHAR(255) DEFAULT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `views_count` INT(11) DEFAULT 0,
    `coupons_count` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Coupons Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `coupons` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `store_id` INT(11) UNSIGNED NOT NULL,
    `title_en` VARCHAR(255) NOT NULL,
    `title_ar` VARCHAR(255) DEFAULT NULL,
    `description_en` TEXT DEFAULT NULL,
    `description_ar` TEXT DEFAULT NULL,
    `code` VARCHAR(50) DEFAULT NULL,
    `discount_type` ENUM('percentage', 'fixed', 'freebie', 'deal') DEFAULT 'percentage',
    `discount_value` DECIMAL(10,2) DEFAULT NULL,
    `affiliate_url` VARCHAR(500) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `start_date` DATE DEFAULT NULL,
    `expiry_date` DATE DEFAULT NULL,
    `is_verified` TINYINT(1) DEFAULT 0,
    `is_exclusive` TINYINT(1) DEFAULT 0,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `views_count` INT(11) DEFAULT 0,
    `uses_count` INT(11) DEFAULT 0,
    `success_rate` TINYINT(3) DEFAULT 100,
    `created_by` INT(11) UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_store` (`store_id`),
    INDEX `idx_expiry` (`expiry_date`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_verified` (`is_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Coupon Categories (Many-to-Many)
-- =====================================================
CREATE TABLE IF NOT EXISTS `coupon_categories` (
    `coupon_id` INT(11) UNSIGNED NOT NULL,
    `category_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`coupon_id`, `category_id`),
    FOREIGN KEY (`coupon_id`) REFERENCES `coupons`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Articles Table (Blog System)
-- =====================================================
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title_en` VARCHAR(255) NOT NULL,
    `title_ar` VARCHAR(255) DEFAULT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt_en` TEXT DEFAULT NULL,
    `excerpt_ar` TEXT DEFAULT NULL,
    `content_en` LONGTEXT DEFAULT NULL,
    `content_ar` LONGTEXT DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `category_id` INT(11) UNSIGNED DEFAULT NULL,
    `author_id` INT(11) UNSIGNED DEFAULT NULL,
    `seo_title` VARCHAR(255) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `seo_keywords` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('draft', 'published', 'scheduled') DEFAULT 'draft',
    `scheduled_at` DATETIME DEFAULT NULL,
    `views_count` INT(11) DEFAULT 0,
    `is_featured` TINYINT(1) DEFAULT 0,
    `allow_comments` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `published_at` DATETIME DEFAULT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `article_categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Article Categories
-- =====================================================
CREATE TABLE IF NOT EXISTS `article_categories` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_en` VARCHAR(100) NOT NULL,
    `name_ar` VARCHAR(100) DEFAULT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description_en` TEXT DEFAULT NULL,
    `description_ar` TEXT DEFAULT NULL,
    `parent_id` INT(11) UNSIGNED DEFAULT NULL,
    `order_position` INT(11) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `article_categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Article Tags
-- =====================================================
CREATE TABLE IF NOT EXISTS `tags` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_en` VARCHAR(50) NOT NULL,
    `name_ar` VARCHAR(50) DEFAULT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Article Tags (Many-to-Many)
-- =====================================================
CREATE TABLE IF NOT EXISTS `article_tags` (
    `article_id` INT(11) UNSIGNED NOT NULL,
    `tag_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`article_id`, `tag_id`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Settings Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` LONGTEXT DEFAULT NULL,
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`setting_key`),
    INDEX `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Email Subscriptions
-- =====================================================
CREATE TABLE IF NOT EXISTS `subscribers` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` DATETIME DEFAULT NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Pages Table (Static Pages)
-- =====================================================
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title_en` VARCHAR(255) NOT NULL,
    `title_ar` VARCHAR(255) DEFAULT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content_en` LONGTEXT DEFAULT NULL,
    `content_ar` LONGTEXT DEFAULT NULL,
    `seo_title` VARCHAR(255) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert Default Admin User (password: admin123)
-- =====================================================
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `is_active`) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Site Administrator', 'admin', 1);

-- =====================================================
-- Insert Default Settings
-- =====================================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
-- General Settings
('site_name', 'Coupon & Deals', 'general'),
('site_tagline', 'Best Coupons & Deals Online', 'general'),
('site_email', 'info@example.com', 'general'),
('site_phone', '', 'general'),
('site_address', '', 'general'),
('default_language', 'en', 'general'),

-- Appearance Settings
('site_logo', '', 'appearance'),
('site_favicon', '', 'appearance'),
('primary_color', '#6366f1', 'appearance'),
('secondary_color', '#8b5cf6', 'appearance'),
('accent_color', '#ec4899', 'appearance'),
('gradient_color_1', '#6366f1', 'appearance'),
('gradient_color_2', '#8b5cf6', 'appearance'),
('gradient_color_3', '#ec4899', 'appearance'),
('header_style', 'default', 'appearance'),
('font_size', 'medium', 'appearance'),
('hero_title_en', 'Find the Best Deals & Coupons', 'appearance'),
('hero_title_ar', 'اعثر على أفضل العروض والكوبونات', 'appearance'),
('hero_subtitle_en', 'Save money with exclusive discount codes from your favorite stores', 'appearance'),
('hero_subtitle_ar', 'وفر المال مع أكواد الخصم الحصرية من متاجرك المفضلة', 'appearance'),
('hero_image', '', 'appearance'),
('footer_text', '© 2024 Coupon & Deals. All rights reserved.', 'appearance'),

-- SEO Settings
('meta_title', 'Coupon & Deals - Best Online Coupons', 'seo'),
('meta_description', 'Find the best coupons, promo codes, and deals from top stores. Save money on your online shopping.', 'seo'),
('meta_keywords', 'coupons, deals, promo codes, discounts, offers', 'seo'),
('og_image', '', 'seo'),
('twitter_handle', '', 'seo'),
('google_analytics', '', 'seo'),
('schema_org_enabled', '1', 'seo'),
('sitemap_enabled', '1', 'seo'),

-- Social Links
('facebook_url', '', 'social'),
('twitter_url', '', 'social'),
('instagram_url', '', 'social'),
('youtube_url', '', 'social'),
('pinterest_url', '', 'social');

-- =====================================================
-- Insert Sample Categories
-- =====================================================
INSERT INTO `categories` (`name_en`, `name_ar`, `slug`, `description_en`, `icon`, `is_active`) VALUES
('Fashion', 'أزياء', 'fashion', 'Clothing, shoes, and accessories deals', 'fa-tshirt', 1),
('Electronics', 'إلكترونيات', 'electronics', 'Gadgets, phones, computers, and tech deals', 'fa-laptop', 1),
('Food & Dining', 'طعام ومطاعم', 'food-dining', 'Restaurant and food delivery coupons', 'fa-utensils', 1),
('Travel', 'سفر', 'travel', 'Hotels, flights, and vacation deals', 'fa-plane', 1),
('Beauty', 'جمال', 'beauty', 'Cosmetics, skincare, and beauty products', 'fa-spa', 1),
('Health & Fitness', 'صحة ولياقة', 'health-fitness', 'Gym, vitamins, and wellness deals', 'fa-heartbeat', 1),
('Home & Garden', 'منزل وحديقة', 'home-garden', 'Furniture, decor, and home improvement', 'fa-home', 1),
('Entertainment', 'ترفيه', 'entertainment', 'Movies, games, and subscription services', 'fa-gamepad', 1);

-- =====================================================
-- Insert Sample Stores
-- =====================================================
INSERT INTO `stores` (`name`, `slug`, `description_en`, `description_ar`, `website_url`, `category_id`, `is_featured`, `is_active`) VALUES
('Amazon', 'amazon', 'World''s largest online retailer offering millions of products', 'أكبر بائع تجزئة عبر الإنترنت في العالم', 'https://amazon.com', 2, 1, 1),
('Nike', 'nike', 'Global leader in athletic footwear and apparel', 'رائد عالمي في الأحذية والملابس الرياضية', 'https://nike.com', 1, 1, 1),
('Booking.com', 'booking', 'Leading online travel agency for hotels and accommodations', 'وكالة سفر رائدة عبر الإنترنت للفنادق', 'https://booking.com', 4, 1, 1),
('Uber Eats', 'uber-eats', 'Food delivery from your favorite restaurants', 'توصيل طعام من مطاعمك المفضلة', 'https://ubereats.com', 3, 0, 1),
('Sephora', 'sephora', 'Premium beauty retailer with exclusive products', 'بائع تجزئة للجمال مع منتجات حصرية', 'https://sephora.com', 5, 1, 1);

-- =====================================================
-- Insert Sample Coupons
-- =====================================================
INSERT INTO `coupons` (`store_id`, `title_en`, `title_ar`, `description_en`, `code`, `discount_type`, `discount_value`, `expiry_date`, `is_verified`, `is_exclusive`, `is_featured`, `is_active`) VALUES
(1, '20% Off Electronics', 'خصم 20% على الإلكترونيات', 'Get 20% off on all electronics purchases', 'ELEC20', 'percentage', 20.00, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 0, 1, 1),
(1, 'Free Shipping Over $50', 'شحن مجاني للطلبات فوق 50$', 'Free shipping on orders over $50', 'FREESHIP50', 'freebie', NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 1, 0, 0, 1),
(2, '30% Off Sale Items', 'خصم 30% على عناصر التخفيضات', 'Extra 30% off all sale items', 'SALE30', 'percentage', 30.00, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 1, 1, 1, 1),
(2, '$25 Off $100 Purchase', 'خصم 25$ على مشتريات 100$', 'Save $25 when you spend $100 or more', 'SAVE25', 'fixed', 25.00, DATE_ADD(CURDATE(), INTERVAL 45 DAY), 1, 0, 0, 1),
(3, '15% Off Hotel Bookings', 'خصم 15% على حجوزات الفنادق', 'Get 15% off on hotel bookings worldwide', 'HOTEL15', 'percentage', 15.00, DATE_ADD(CURDATE(), INTERVAL 90 DAY), 1, 1, 1, 1),
(4, '$10 Off First Order', 'خصم 10$ على الطلب الأول', 'New users get $10 off first food order', 'FIRST10', 'fixed', 10.00, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 0, 1, 1),
(5, 'Free Gift with Purchase', 'هدية مجانية مع الشراء', 'Get a free mini product with any $50 purchase', 'GIFT50', 'freebie', NULL, DATE_ADD(CURDATE(), INTERVAL 21 DAY), 1, 1, 0, 1);

-- =====================================================
-- Insert Article Categories
-- =====================================================
INSERT INTO `article_categories` (`name_en`, `name_ar`, `slug`, `is_active`) VALUES
('Shopping Tips', 'نصائح التسوق', 'shopping-tips', 1),
('Deal Alerts', 'تنبيهات العروض', 'deal-alerts', 1),
('Saving Guides', 'أدلة التوفير', 'saving-guides', 1),
('Store Reviews', 'مراجعات المتاجر', 'store-reviews', 1);

-- =====================================================
-- Insert Sample Articles
-- =====================================================
INSERT INTO `articles` (`title_en`, `title_ar`, `slug`, `excerpt_en`, `content_en`, `category_id`, `author_id`, `status`, `is_featured`, `published_at`) VALUES
('Top 10 Ways to Save Money Online', 'أفضل 10 طرق للتوفير عند التسوق عبر الإنترنت', 'top-10-ways-save-money-online', 
'Discover the best strategies to save money while shopping online. Learn how to use coupons, cashback, and more.',
'<h2>Introduction</h2><p>Online shopping offers countless opportunities to save money if you know where to look. In this guide, we''ll share our top strategies for maximizing your savings.</p><h2>1. Use Coupon Codes</h2><p>Always search for coupon codes before completing your purchase. Websites like ours compile the best deals from top stores.</p><h2>2. Sign Up for Newsletters</h2><p>Many stores offer exclusive discounts to email subscribers. Sign up to get deals delivered to your inbox.</p><h2>3. Use Cashback Services</h2><p>Cashback sites give you a percentage of your purchase back as cash. Stack this with coupons for maximum savings.</p>',
1, 1, 'published', 1, NOW()),
('Best Black Friday Deals 2024', 'أفضل عروض الجمعة السوداء 2024', 'best-black-friday-deals-2024',
'Get ready for the biggest shopping event of the year with our comprehensive Black Friday deals guide.',
'<h2>Black Friday 2024 is Coming!</h2><p>Mark your calendars for the biggest shopping event of the year. We''ve compiled all the best deals from major retailers.</p><h2>Top Electronics Deals</h2><p>Expect massive discounts on TVs, laptops, and smartphones from Amazon, Best Buy, and more.</p><h2>Fashion Steals</h2><p>Major fashion retailers like Nike, Adidas, and Zara will offer up to 70% off.</p>',
2, 1, 'published', 1, NOW());

-- =====================================================
-- Insert Sample Tags
-- =====================================================
INSERT INTO `tags` (`name_en`, `name_ar`, `slug`) VALUES
('Black Friday', 'الجمعة السوداء', 'black-friday'),
('Cyber Monday', 'اثنين الإنترنت', 'cyber-monday'),
('Free Shipping', 'شحن مجاني', 'free-shipping'),
('Exclusive', 'حصري', 'exclusive'),
('Limited Time', 'وقت محدود', 'limited-time');
