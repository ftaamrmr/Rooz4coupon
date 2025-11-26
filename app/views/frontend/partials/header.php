<?php
/**
 * Frontend Header Layout
 * Main header template with navigation
 */

$siteName = getSetting('site_name', 'Coupon & Deals');
$siteLogo = getSetting('site_logo');
$primaryColor = getSetting('primary_color', '#6366f1');
$gradientColor1 = getSetting('gradient_color_1', '#6366f1');
$gradientColor2 = getSetting('gradient_color_2', '#8b5cf6');
$gradientColor3 = getSetting('gradient_color_3', '#ec4899');
$isRtl = isRTL();
$direction = $isRtl ? 'rtl' : 'ltr';
$lang = getCurrentLang();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $direction ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <?= generateMetaTags($pageTitle ?? '', $pageDescription ?? '', $pageImage ?? '') ?>
    
    <!-- Favicon -->
    <?php if ($favicon = getSetting('site_favicon')): ?>
        <link rel="icon" href="<?= e(upload($favicon)) ?>" type="image/x-icon">
    <?php endif; ?>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '<?= $primaryColor ?>',
                    },
                    fontFamily: {
                        <?php if ($isRtl): ?>
                        'sans': ['Cairo', 'Tajawal', 'sans-serif'],
                        <?php else: ?>
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        <?php endif; ?>
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <?php if ($isRtl): ?>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php else: ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php endif; ?>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --gradient-start: <?= $gradientColor1 ?>;
            --gradient-mid: <?= $gradientColor2 ?>;
            --gradient-end: <?= $gradientColor3 ?>;
        }
        .gradient-bg {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-mid) 50%, var(--gradient-end) 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-mid) 50%, var(--gradient-end) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .coupon-code {
            border: 2px dashed currentColor;
            position: relative;
        }
        .coupon-code::before,
        .coupon-code::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: var(--bg-color, #fff);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }
        .coupon-code::before { left: -12px; }
        .coupon-code::after { right: -12px; }
        
        /* Dark mode coupon fix */
        .dark .coupon-code::before,
        .dark .coupon-code::after {
            background: #1f2937;
        }
        
        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color;
            transition-duration: 150ms;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }
    </style>
    
    <?php if (getSetting('google_analytics')): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(getSetting('google_analytics')) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= e(getSetting('google_analytics')) ?>');
    </script>
    <?php endif; ?>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col">
    <!-- Top Bar -->
    <div class="gradient-bg text-white py-2 text-sm">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span><i class="fas fa-fire mr-1"></i> <?= __('Hot Deals Available!', 'عروض ساخنة متاحة!') ?></span>
            </div>
            <div class="flex items-center gap-4">
                <!-- Language Switcher -->
                <div class="flex items-center gap-2">
                    <a href="<?= url('lang/en') ?>" class="<?= $lang === 'en' ? 'font-bold' : 'opacity-75 hover:opacity-100' ?>">EN</a>
                    <span>|</span>
                    <a href="<?= url('lang/ar') ?>" class="<?= $lang === 'ar' ? 'font-bold' : 'opacity-75 hover:opacity-100' ?>">عربي</a>
                </div>
                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="opacity-75 hover:opacity-100" title="<?= __('Toggle Dark Mode', 'تبديل الوضع المظلم') ?>">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <a href="<?= url('/') ?>" class="flex items-center gap-2">
                    <?php if ($siteLogo): ?>
                        <img src="<?= e(upload($siteLogo)) ?>" alt="<?= e($siteName) ?>" class="h-10">
                    <?php else: ?>
                        <span class="text-2xl font-bold gradient-text"><?= e($siteName) ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- Search Bar (Desktop) -->
                <div class="hidden md:flex flex-1 max-w-xl mx-8">
                    <form action="<?= url('search') ?>" method="GET" class="w-full relative">
                        <input type="text" name="q" placeholder="<?= __('Search for stores, coupons...', 'ابحث عن متاجر، كوبونات...') ?>" 
                               class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-search absolute <?= $isRtl ? 'right-3' : 'left-3' ?> top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </form>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden lg:flex items-center gap-6">
                    <a href="<?= url('/') ?>" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                        <?= __('Home', 'الرئيسية') ?>
                    </a>
                    <a href="<?= url('stores') ?>" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                        <?= __('Stores', 'المتاجر') ?>
                    </a>
                    <a href="<?= url('categories') ?>" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                        <?= __('Categories', 'الفئات') ?>
                    </a>
                    <a href="<?= url('coupons') ?>" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                        <?= __('Coupons', 'كوبونات') ?>
                    </a>
                    <a href="<?= url('blog') ?>" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                        <?= __('Blog', 'المدونة') ?>
                    </a>
                </nav>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="lg:hidden text-gray-700 dark:text-gray-200">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white dark:bg-gray-800 border-t dark:border-gray-700">
            <div class="container mx-auto px-4 py-4">
                <!-- Mobile Search -->
                <form action="<?= url('search') ?>" method="GET" class="mb-4">
                    <input type="text" name="q" placeholder="<?= __('Search...', 'بحث...') ?>" 
                           class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                </form>
                <!-- Mobile Nav Links -->
                <nav class="flex flex-col gap-3">
                    <a href="<?= url('/') ?>" class="text-gray-700 dark:text-gray-200 py-2"><?= __('Home', 'الرئيسية') ?></a>
                    <a href="<?= url('stores') ?>" class="text-gray-700 dark:text-gray-200 py-2"><?= __('Stores', 'المتاجر') ?></a>
                    <a href="<?= url('categories') ?>" class="text-gray-700 dark:text-gray-200 py-2"><?= __('Categories', 'الفئات') ?></a>
                    <a href="<?= url('coupons') ?>" class="text-gray-700 dark:text-gray-200 py-2"><?= __('Coupons', 'كوبونات') ?></a>
                    <a href="<?= url('blog') ?>" class="text-gray-700 dark:text-gray-200 py-2"><?= __('Blog', 'المدونة') ?></a>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Flash Messages -->
    <?php if ($flash = getFlash()): ?>
    <div class="container mx-auto px-4 mt-4">
        <div class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow">
