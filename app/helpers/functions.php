<?php
/**
 * Helper Functions
 */

/**
 * Sanitize input string
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate SEO-friendly slug
 */
function generateSlug($string, $separator = '-') {
    // Convert to lowercase
    $string = mb_strtolower($string, 'UTF-8');
    
    // Replace non-alphanumeric characters with separator
    $string = preg_replace('/[^a-z0-9\-]/', $separator, $string);
    
    // Remove multiple consecutive separators
    $string = preg_replace('/' . preg_quote($separator) . '+/', $separator, $string);
    
    // Trim separators from beginning and end
    return trim($string, $separator);
}

/**
 * Generate unique slug for database
 */
function generateUniqueSlug($table, $title, $excludeId = null) {
    $db = getDB();
    $baseSlug = generateSlug($title);
    $slug = $baseSlug;
    $counter = 1;
    
    while (true) {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->fetchColumn() == 0) {
            break;
        }
        
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Get current URL
 */
function currentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Check if current page matches
 */
function isCurrentPage($path) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return strpos($currentPath, $path) !== false;
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Time ago format
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}

/**
 * Calculate days remaining until expiry
 */
function daysRemaining($expiryDate) {
    if (empty($expiryDate)) return null;
    
    $expiry = strtotime($expiryDate);
    $now = time();
    $diff = $expiry - $now;
    
    if ($diff < 0) return -1; // Expired
    
    return ceil($diff / 86400);
}

/**
 * Format discount
 */
function formatDiscount($type, $value) {
    switch ($type) {
        case 'percentage':
            return $value . '% OFF';
        case 'fixed':
            return '$' . number_format($value, 2) . ' OFF';
        case 'free_shipping':
            return 'Free Shipping';
        default:
            return 'Special Deal';
    }
}

/**
 * Truncate text
 */
function truncate($text, $length = 150, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get setting value
 */
function getSetting($key, $default = '') {
    static $settings = null;
    
    if ($settings === null) {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (Exception $e) {
            $settings = [];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Update setting value
 */
function updateSetting($key, $value, $group = 'general') {
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) 
                          VALUES (?, ?, ?) 
                          ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
    return $stmt->execute([$key, $value, $group, $value]);
}

/**
 * Get language text
 */
function __($key, $lang = null) {
    static $translations = [];
    
    if ($lang === null) {
        $lang = $_SESSION['language'] ?? DEFAULT_LANGUAGE;
    }
    
    if (!isset($translations[$lang])) {
        $langFile = APP_PATH . "/lang/{$lang}.php";
        if (file_exists($langFile)) {
            $translations[$lang] = include $langFile;
        } else {
            $translations[$lang] = [];
        }
    }
    
    return $translations[$lang][$key] ?? $key;
}

/**
 * Check if RTL language
 */
function isRTL() {
    $lang = $_SESSION['language'] ?? DEFAULT_LANGUAGE;
    return in_array($lang, ['ar', 'he', 'fa', 'ur']);
}

/**
 * Get current language
 */
function getCurrentLanguage() {
    return $_SESSION['language'] ?? DEFAULT_LANGUAGE;
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        $typeClasses = [
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700'
        ];
        $class = $typeClasses[$flash['type']] ?? $typeClasses['info'];
        
        return '<div class="' . $class . ' px-4 py-3 rounded border mb-4" role="alert">
                    <span class="block sm:inline">' . htmlspecialchars($flash['message']) . '</span>
                </div>';
    }
    return '';
}

/**
 * Pagination helper
 */
function paginate($total, $perPage, $currentPage, $baseUrl) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'previous_url' => $currentPage > 1 ? $baseUrl . '?page=' . ($currentPage - 1) : null,
        'next_url' => $currentPage < $totalPages ? $baseUrl . '?page=' . ($currentPage + 1) : null,
        'offset' => ($currentPage - 1) * $perPage
    ];
}

/**
 * Generate pagination HTML
 */
function paginationHtml($pagination, $baseUrl) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<nav class="flex justify-center mt-8"><ul class="inline-flex -space-x-px">';
    
    // Previous button
    if ($pagination['has_previous']) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($pagination['current_page'] - 1) . '" 
                  class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a></li>';
    }
    
    // Page numbers
    $start = max(1, $pagination['current_page'] - 2);
    $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $pagination['current_page']) {
            $html .= '<li><span class="px-3 py-2 text-blue-600 border border-gray-300 bg-blue-50 dark:border-gray-700 dark:bg-gray-700 dark:text-white">' . $i . '</span></li>';
        } else {
            $html .= '<li><a href="' . $baseUrl . '?page=' . $i . '" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">' . $i . '</a></li>';
        }
    }
    
    // Next button
    if ($pagination['has_next']) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($pagination['current_page'] + 1) . '" 
                  class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Handle file upload
 */
function uploadFile($file, $directory = 'uploads', $allowedTypes = null) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error: ' . $file['error']];
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File too large. Maximum size is ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB'];
    }
    
    // Validate MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    $allowedTypes = $allowedTypes ?? ALLOWED_IMAGE_TYPES;
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Invalid file extension'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $extension;
    
    // Create directory if not exists
    $uploadDir = PUBLIC_PATH . '/' . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filepath = $uploadDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $directory . '/' . $filename,
            'url' => ASSETS_URL . '/' . $directory . '/' . $filename
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

/**
 * Delete uploaded file
 */
function deleteFile($path) {
    $fullPath = PUBLIC_PATH . '/' . $path;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Generate breadcrumbs
 */
function breadcrumbs($items) {
    $html = '<nav class="flex mb-4" aria-label="Breadcrumb">';
    $html .= '<ol class="inline-flex items-center space-x-1 md:space-x-3">';
    
    $count = count($items);
    foreach ($items as $i => $item) {
        $isLast = ($i === $count - 1);
        
        if ($i > 0) {
            $html .= '<li class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>';
        } else {
            $html .= '<li class="inline-flex items-center">';
        }
        
        if ($isLast) {
            $html .= '<span class="text-gray-500 dark:text-gray-400">' . htmlspecialchars($item['title']) . '</span>';
        } else {
            $html .= '<a href="' . htmlspecialchars($item['url']) . '" class="text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">' . htmlspecialchars($item['title']) . '</a>';
        }
        
        $html .= '</li>';
    }
    
    $html .= '</ol></nav>';
    
    return $html;
}

/**
 * Generate Schema.org JSON-LD
 */
function schemaJsonLd($type, $data) {
    $schema = ['@context' => 'https://schema.org', '@type' => $type];
    $schema = array_merge($schema, $data);
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

/**
 * Auto-expire coupons based on expiry date
 */
function autoExpireCoupons() {
    $db = getDB();
    $stmt = $db->prepare("UPDATE coupons SET status = 'expired' WHERE expiry_date < CURDATE() AND status = 'active'");
    return $stmt->execute();
}
