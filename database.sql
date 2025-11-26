-- Coupons & Deals Website Database Schema
-- Compatible with MySQL 5.7+ and PHP 8+

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Database: `coupon_website`
--

CREATE DATABASE IF NOT EXISTS `coupon_website` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `coupon_website`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor','writer') NOT NULL DEFAULT 'writer',
  `full_name` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin user (password: password)
-- ⚠️ IMPORTANT: Change this password immediately after first login!
-- The hash below is for "password" - you must update it via admin panel
INSERT INTO `users` (`username`, `email`, `password`, `role`, `full_name`, `status`) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default categories
INSERT INTO `categories` (`name`, `name_ar`, `slug`, `description`, `icon`, `sort_order`, `status`) VALUES
('Electronics', 'إلكترونيات', 'electronics', 'Best deals on electronics and gadgets', 'fa-laptop', 1, 'active'),
('Fashion', 'موضة', 'fashion', 'Latest fashion deals and discounts', 'fa-tshirt', 2, 'active'),
('Food & Dining', 'طعام ومطاعم', 'food-dining', 'Restaurant and food delivery coupons', 'fa-utensils', 3, 'active'),
('Travel', 'سفر', 'travel', 'Travel and vacation deals', 'fa-plane', 4, 'active'),
('Beauty', 'جمال', 'beauty', 'Beauty and personal care coupons', 'fa-spa', 5, 'active'),
('Home & Garden', 'منزل وحديقة', 'home-garden', 'Home improvement and garden deals', 'fa-home', 6, 'active'),
('Sports', 'رياضة', 'sports', 'Sports equipment and apparel deals', 'fa-running', 7, 'active'),
('Health', 'صحة', 'health', 'Health and wellness coupons', 'fa-heartbeat', 8, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `affiliate_url` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default stores
INSERT INTO `stores` (`name`, `name_ar`, `slug`, `description`, `logo`, `website`, `category_id`, `is_featured`, `is_popular`, `status`) VALUES
('Amazon', 'أمازون', 'amazon', 'Shop online for electronics, computers, clothing, shoes, toys, books, games and more.', NULL, 'https://amazon.com', 1, 1, 1, 'active'),
('eBay', 'إيباي', 'ebay', 'Buy and sell electronics, cars, clothing, apparel, collectibles, sporting goods.', NULL, 'https://ebay.com', 1, 1, 1, 'active'),
('Nike', 'نايكي', 'nike', 'Nike delivers innovative products, experiences and services to inspire athletes.', NULL, 'https://nike.com', 2, 1, 0, 'active'),
('Booking.com', 'بوكينج', 'booking', 'Great hotel and accommodation deals for your travel needs.', NULL, 'https://booking.com', 4, 1, 1, 'active'),
('Sephora', 'سيفورا', 'sephora', 'Shop makeup, skincare, hair care, fragrances and more.', NULL, 'https://sephora.com', 5, 0, 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `discount_type` enum('percentage','fixed','free_shipping','other') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) DEFAULT NULL,
  `store_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `affiliate_url` varchar(500) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_exclusive` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','expired','pending') NOT NULL DEFAULT 'active',
  `views_count` int(11) NOT NULL DEFAULT 0,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `success_rate` int(11) NOT NULL DEFAULT 100,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `store_id` (`store_id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `expiry_date` (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample coupons
INSERT INTO `coupons` (`title`, `title_ar`, `slug`, `description`, `code`, `discount_type`, `discount_value`, `store_id`, `category_id`, `expiry_date`, `is_featured`, `is_verified`, `status`) VALUES
('20% Off Electronics', '20% خصم على الإلكترونيات', '20-off-electronics-amazon', 'Get 20% off on all electronics at Amazon', 'ELEC20', 'percentage', 20.00, 1, 1, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 1, 'active'),
('Free Shipping on Orders $50+', 'شحن مجاني للطلبات فوق 50$', 'free-shipping-ebay', 'Enjoy free shipping on orders over $50', 'FREESHIP50', 'free_shipping', NULL, 2, 1, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 1, 1, 'active'),
('$10 Off First Purchase', '10$ خصم على أول طلب', '10-off-nike-first-order', 'Get $10 off your first Nike purchase', 'NIKE10NEW', 'fixed', 10.00, 3, 2, DATE_ADD(CURDATE(), INTERVAL 45 DAY), 0, 1, 'active'),
('15% Off Hotel Bookings', '15% خصم على حجز الفنادق', '15-off-booking-hotels', 'Save 15% on your next hotel booking', 'HOTEL15', 'percentage', 15.00, 4, 4, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 1, 1, 'active'),
('25% Off Beauty Products', '25% خصم على منتجات التجميل', '25-off-sephora-beauty', 'Get 25% off all beauty products', 'BEAUTY25', 'percentage', 25.00, 5, 5, DATE_ADD(CURDATE(), INTERVAL 20 DAY), 0, 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `excerpt_ar` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `content_ar` longtext DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `status` enum('published','draft','scheduled') NOT NULL DEFAULT 'draft',
  `publish_date` datetime DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`),
  KEY `status` (`status`),
  KEY `publish_date` (`publish_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_tags`
--

CREATE TABLE `article_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `name_ar` varchar(50) DEFAULT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_tag_relations`
--

CREATE TABLE `article_tag_relations` (
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`article_id`, `tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
-- General settings
('site_name', 'CouponHub', 'general'),
('site_name_ar', 'كوبون هب', 'general'),
('site_tagline', 'Best Coupons & Deals', 'general'),
('site_tagline_ar', 'أفضل الكوبونات والعروض', 'general'),
('site_email', 'contact@example.com', 'general'),
('site_phone', '', 'general'),
('default_language', 'en', 'general'),
('timezone', 'UTC', 'general'),
('date_format', 'Y-m-d', 'general'),
('session_timeout', '30', 'general'),

-- Appearance settings
('logo', '', 'appearance'),
('logo_dark', '', 'appearance'),
('favicon', '', 'appearance'),
('primary_color', '#3b82f6', 'appearance'),
('secondary_color', '#1e40af', 'appearance'),
('accent_color', '#f59e0b', 'appearance'),
('gradient_start', '#3b82f6', 'appearance'),
('gradient_middle', '#6366f1', 'appearance'),
('gradient_end', '#8b5cf6', 'appearance'),
('header_style', 'default', 'appearance'),
('font_family', 'Inter', 'appearance'),
('font_size_base', '16', 'appearance'),
('hero_title', 'Find the Best Deals', 'appearance'),
('hero_title_ar', 'اكتشف أفضل العروض', 'appearance'),
('hero_subtitle', 'Save money with exclusive coupons and discounts', 'appearance'),
('hero_subtitle_ar', 'وفر المال مع كوبونات وخصومات حصرية', 'appearance'),
('hero_bg_image', '', 'appearance'),
('footer_text', '© 2024 CouponHub. All rights reserved.', 'appearance'),
('footer_text_ar', '© 2024 كوبون هب. جميع الحقوق محفوظة.', 'appearance'),

-- SEO settings
('meta_title', 'CouponHub - Best Coupons & Deals Online', 'seo'),
('meta_description', 'Discover the best coupons, promo codes, and deals from top stores. Save money on your online shopping with verified discount codes.', 'seo'),
('meta_keywords', 'coupons, deals, promo codes, discounts, online shopping, savings', 'seo'),
('og_image', '', 'seo'),
('twitter_handle', '', 'seo'),
('facebook_url', '', 'seo'),
('google_analytics', '', 'seo'),
('google_site_verification', '', 'seo'),
('bing_site_verification', '', 'seo'),
('schema_organization', '{"@type":"Organization","name":"CouponHub"}', 'seo'),

-- Social media
('social_facebook', '', 'social'),
('social_twitter', '', 'social'),
('social_instagram', '', 'social'),
('social_youtube', '', 'social'),
('social_linkedin', '', 'social'),
('social_tiktok', '', 'social'),

-- Ad slots
('ad_header', '', 'ads'),
('ad_sidebar', '', 'ads'),
('ad_footer', '', 'ads'),
('ad_article_top', '', 'ads'),
('ad_article_bottom', '', 'ads'),
('ad_coupon_sidebar', '', 'ads');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `status` enum('active','unsubscribed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_reports`
--

CREATE TABLE `coupon_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `report_type` enum('not_working','expired','wrong_code','other') NOT NULL,
  `comment` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` enum('pending','reviewed','resolved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `coupon_id` (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Foreign Key Constraints
--

ALTER TABLE `categories`
  ADD CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `stores`
  ADD CONSTRAINT `fk_store_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `coupons`
  ADD CONSTRAINT `fk_coupon_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_coupon_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_coupon_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `articles`
  ADD CONSTRAINT `fk_article_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_article_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `article_tag_relations`
  ADD CONSTRAINT `fk_article_tag_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_article_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `article_tags` (`id`) ON DELETE CASCADE;

ALTER TABLE `coupon_reports`
  ADD CONSTRAINT `fk_report_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE;

ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
