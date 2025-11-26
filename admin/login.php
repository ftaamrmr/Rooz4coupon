<?php
/**
 * Admin Login Page
 */

require_once __DIR__ . '/../config/config.php';
require_once APP_PATH . '/models/User.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(ADMIN_URL . '/dashboard.php');
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Rate limiting check
    if (!checkRateLimit('login_' . $_SERVER['REMOTE_ADDR'], 5, 300)) {
        $error = 'Too many login attempts. Please try again in 5 minutes.';
    } else {
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            loginUser($user);
            setFlash('success', 'Welcome back, ' . ($user['full_name'] ?? $user['username']) . '!');
            redirect(ADMIN_URL . '/dashboard.php');
        } else {
            $error = 'Invalid username or password.';
            logActivity('login_failed', 'Failed login attempt for: ' . $username);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo getSetting('site_name', 'CouponHub'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6, #6366f1, #8b5cf6);
        }
    </style>
</head>
<body class="h-full bg-gray-100">
    <div class="min-h-full flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg items-center justify-center">
            <div class="text-center text-white p-12">
                <h1 class="text-5xl font-bold mb-4"><?php echo getSetting('site_name', 'CouponHub'); ?></h1>
                <p class="text-xl opacity-90 mb-8">Admin Dashboard</p>
                <div class="flex justify-center space-x-8">
                    <div class="text-center">
                        <i class="fas fa-ticket-alt text-4xl mb-2"></i>
                        <p class="text-sm">Manage Coupons</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-store text-4xl mb-2"></i>
                        <p class="text-sm">Manage Stores</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-newspaper text-4xl mb-2"></i>
                        <p class="text-sm">Blog Articles</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="text-center mb-8">
                        <div class="lg:hidden mb-6">
                            <h1 class="text-3xl font-bold text-gray-800"><?php echo getSetting('site_name', 'CouponHub'); ?></h1>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
                        <p class="text-gray-500 mt-2">Sign in to your admin account</p>
                    </div>
                    
                    <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="space-y-6">
                        <?php echo csrfField(); ?>
                        
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username or Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="username" id="username" required
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your username">
                            </div>
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" id="password" required
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your password">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="password-toggle-icon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </form>
                    
                    <div class="mt-8 text-center text-sm text-gray-500">
                        <a href="<?php echo BASE_URL; ?>" class="text-blue-600 hover:underline">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Website
                        </a>
                    </div>
                </div>
                
                <p class="text-center text-gray-400 text-sm mt-8">
                    Default credentials: admin / password
                </p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
