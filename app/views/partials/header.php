<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>" dir="<?php echo isRTL() ? 'rtl' : 'ltr'; ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title><?php echo htmlspecialchars($pageTitle ?? getSetting('meta_title', 'CouponHub')); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription ?? getSetting('meta_description', '')); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords ?? getSetting('meta_keywords', '')); ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo currentUrl(); ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle ?? getSetting('meta_title')); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription ?? getSetting('meta_description')); ?>">
    <meta property="og:url" content="<?php echo currentUrl(); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo getSetting('og_image') ? UPLOAD_URL . '/' . getSetting('og_image') : ASSETS_URL . '/images/og-default.jpg'; ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle ?? getSetting('meta_title')); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription ?? getSetting('meta_description')); ?>">
    
    <!-- Favicon -->
    <?php if (getSetting('favicon')): ?>
    <link rel="icon" href="<?php echo UPLOAD_URL . '/' . getSetting('favicon'); ?>">
    <?php endif; ?>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo getSetting('primary_color', '#3b82f6'); ?>',
                        secondary: '<?php echo getSetting('secondary_color', '#1e40af'); ?>',
                        accent: '<?php echo getSetting('accent_color', '#f59e0b'); ?>'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    
    <style>
        body {
            font-family: '<?php echo isRTL() ? 'Noto Sans Arabic' : 'Inter'; ?>', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, <?php echo getSetting('gradient_start', '#3b82f6'); ?>, <?php echo getSetting('gradient_middle', '#6366f1'); ?>, <?php echo getSetting('gradient_end', '#8b5cf6'); ?>);
        }
    </style>
    
    <!-- Google Analytics -->
    <?php if (getSetting('google_analytics')): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo getSetting('google_analytics'); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo getSetting('google_analytics'); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Schema.org Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo getSetting('site_name', 'CouponHub'); ?>",
        "url": "<?php echo BASE_URL; ?>",
        "logo": "<?php echo getSetting('logo') ? UPLOAD_URL . '/' . getSetting('logo') : ''; ?>"
    }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300">
    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 bg-primary text-white p-2">
        Skip to main content
    </a>

    <!-- Header Ad Slot -->
    <?php if (getSetting('ad_header')): ?>
    <div class="w-full bg-gray-100 dark:bg-gray-800 py-2">
        <div class="container mx-auto px-4 text-center">
            <?php echo getSetting('ad_header'); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-white dark:bg-gray-800 shadow-md">
        <nav class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="<?php echo BASE_URL; ?>" class="flex items-center space-x-2 rtl:space-x-reverse">
                    <?php if (getSetting('logo')): ?>
                    <img src="<?php echo UPLOAD_URL . '/' . getSetting('logo'); ?>" alt="<?php echo getSetting('site_name'); ?>" class="h-10 dark:hidden">
                    <img src="<?php echo getSetting('logo_dark') ? UPLOAD_URL . '/' . getSetting('logo_dark') : UPLOAD_URL . '/' . getSetting('logo'); ?>" alt="<?php echo getSetting('site_name'); ?>" class="h-10 hidden dark:block">
                    <?php else: ?>
                    <span class="text-2xl font-bold gradient-bg bg-clip-text text-transparent"><?php echo getSetting('site_name', 'CouponHub'); ?></span>
                    <?php endif; ?>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6 rtl:space-x-reverse">
                    <a href="<?php echo BASE_URL; ?>" class="hover:text-primary transition <?php echo isCurrentPage('/') && !isCurrentPage('/store') && !isCurrentPage('/coupon') ? 'text-primary font-semibold' : ''; ?>">
                        <?php echo __('home'); ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/stores" class="hover:text-primary transition <?php echo isCurrentPage('/stores') ? 'text-primary font-semibold' : ''; ?>">
                        <?php echo __('stores'); ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/coupons" class="hover:text-primary transition <?php echo isCurrentPage('/coupons') ? 'text-primary font-semibold' : ''; ?>">
                        <?php echo __('coupons'); ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/blog" class="hover:text-primary transition <?php echo isCurrentPage('/blog') ? 'text-primary font-semibold' : ''; ?>">
                        <?php echo __('blog'); ?>
                    </a>
                </div>

                <!-- Search & Actions -->
                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                    <!-- Search Button -->
                    <button id="search-toggle" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                        <i class="fas fa-search text-lg"></i>
                    </button>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                        <i class="fas fa-moon dark:hidden text-lg"></i>
                        <i class="fas fa-sun hidden dark:block text-lg text-yellow-400"></i>
                    </button>

                    <!-- Language Toggle -->
                    <a href="<?php echo BASE_URL; ?>/lang/<?php echo getCurrentLanguage() === 'en' ? 'ar' : 'en'; ?>" 
                       class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition text-sm font-medium">
                        <?php echo getCurrentLanguage() === 'en' ? 'عربي' : 'EN'; ?>
                    </a>

                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle" class="md:hidden p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="<?php echo BASE_URL; ?>" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <?php echo __('home'); ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/stores" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <?php echo __('stores'); ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/coupons" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <?php echo __('coupons'); ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/blog" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <?php echo __('blog'); ?>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Search Overlay -->
        <div id="search-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
            <div class="container mx-auto px-4 pt-20">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 max-w-2xl mx-auto">
                    <form action="<?php echo BASE_URL; ?>/search" method="GET" class="relative">
                        <input type="text" name="q" id="search-input" placeholder="<?php echo __('search_placeholder'); ?>"
                               class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-gray-500 hover:text-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div id="search-suggestions" class="mt-4 hidden">
                        <!-- AJAX search results will appear here -->
                    </div>
                    <button id="close-search" class="absolute top-4 right-4 p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="container mx-auto px-4 mt-4">
        <?php echo displayFlash(); ?>
    </div>

    <!-- Main Content -->
    <main id="main-content">
