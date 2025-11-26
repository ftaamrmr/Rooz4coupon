<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard'; ?> - <?php echo getSetting('site_name', 'CouponHub'); ?></title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6, #6366f1, #8b5cf6);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex-shrink-0 hidden lg:block">
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="p-6 border-b">
                    <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="flex items-center space-x-2">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800"><?php echo getSetting('site_name', 'CouponHub'); ?></span>
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                        <i class="fas fa-home w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <div class="pt-4">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Content</p>
                        
                        <a href="<?php echo ADMIN_URL; ?>/coupons/" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/coupons/') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-ticket-alt w-5"></i>
                            <span>Coupons</span>
                        </a>
                        
                        <a href="<?php echo ADMIN_URL; ?>/stores/" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/stores/') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-store w-5"></i>
                            <span>Stores</span>
                        </a>
                        
                        <a href="<?php echo ADMIN_URL; ?>/categories/" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/categories/') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-folder w-5"></i>
                            <span>Categories</span>
                        </a>
                        
                        <a href="<?php echo ADMIN_URL; ?>/articles/" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/articles/') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-newspaper w-5"></i>
                            <span>Articles</span>
                        </a>
                    </div>
                    
                    <?php if (hasRole(['admin'])): ?>
                    <div class="pt-4">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Settings</p>
                        
                        <a href="<?php echo ADMIN_URL; ?>/settings/general.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/settings/general') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-cog w-5"></i>
                            <span>General</span>
                        </a>
                        
                        <a href="<?php echo ADMIN_URL; ?>/settings/appearance.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/settings/appearance') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-paint-brush w-5"></i>
                            <span>Appearance</span>
                        </a>
                        
                        <a href="<?php echo ADMIN_URL; ?>/settings/seo.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/settings/seo') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-search w-5"></i>
                            <span>SEO</span>
                        </a>
                        
                        <a href="<?php echo ADMIN_URL; ?>/users/" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-users w-5"></i>
                            <span>Users</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </nav>
                
                <!-- User Info -->
                <div class="p-4 border-t">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
                            <p class="text-xs text-gray-500 capitalize"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'admin'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-btn" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Search -->
                    <div class="hidden md:block flex-1 max-w-lg mx-4">
                        <div class="relative">
                            <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Right Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- View Site -->
                        <a href="<?php echo BASE_URL; ?>" target="_blank" class="text-gray-500 hover:text-gray-700" title="View Site">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        
                        <!-- Notifications (placeholder) -->
                        <button class="text-gray-500 hover:text-gray-700 relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">3</span>
                        </button>
                        
                        <!-- User Dropdown -->
                        <div class="relative" id="user-dropdown">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900" onclick="toggleDropdown()">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500 text-sm"></i>
                                </div>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="<?php echo ADMIN_URL; ?>/profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="<?php echo ADMIN_URL; ?>/settings/general.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                <hr class="my-2">
                                <a href="<?php echo ADMIN_URL; ?>/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Flash Messages -->
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="mx-6 mt-4">
                <div class="px-4 py-3 rounded-lg <?php echo $flash['type'] === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
