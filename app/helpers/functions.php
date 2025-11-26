<?php
/**
 * Helper Functions
 * Coupons & Deals Website
 */

/**
 * Get setting value from database
 */
function getSetting($key, $default = '') {
    static $settings = null;
    
    if ($settings === null) {
        try {
            $results = db()->fetchAll("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            $settings = [];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Get localized text based on current language
 */
function __($en, $ar = null) {
    if (getCurrentLang() === 'ar' && $ar !== null) {
        return $ar;
    }
    return $en;
}

/**
 * Get localized field from database record
 */
function getLocalizedField($record, $field) {
    $lang = getCurrentLang();
    $localizedField = $field . '_' . $lang;
    $defaultField = $field . '_en';
    
    if (isset($record[$localizedField]) && !empty($record[$localizedField])) {
        return $record[$localizedField];
    }
    
    return $record[$defaultField] ?? $record[$field] ?? '';
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Calculate days remaining until expiry
 */
function daysRemaining($expiryDate) {
    if (empty($expiryDate)) return null;
    
    $expiry = new DateTime($expiryDate);
    $now = new DateTime();
    $diff = $now->diff($expiry);
    
    if ($expiry < $now) {
        return -1; // Expired
    }
    
    return $diff->days;
}

/**
 * Check if coupon is expired
 */
function isExpired($expiryDate) {
    if (empty($expiryDate)) return false;
    return strtotime($expiryDate) < strtotime('today');
}

/**
 * Generate countdown HTML
 */
function generateCountdown($expiryDate) {
    $days = daysRemaining($expiryDate);
    
    if ($days === null) {
        return '<span class="text-green-500">' . __('No Expiry', 'لا ينتهي') . '</span>';
    }
    
    if ($days < 0) {
        return '<span class="text-red-500">' . __('Expired', 'منتهي') . '</span>';
    }
    
    if ($days === 0) {
        return '<span class="text-orange-500">' . __('Expires Today', 'ينتهي اليوم') . '</span>';
    }
    
    if ($days === 1) {
        return '<span class="text-orange-500">' . __('Expires Tomorrow', 'ينتهي غداً') . '</span>';
    }
    
    if ($days <= 7) {
        return '<span class="text-orange-500">' . sprintf(__('%d days left', 'متبقي %d أيام'), $days) . '</span>';
    }
    
    return '<span class="text-green-500">' . sprintf(__('%d days left', 'متبقي %d يوم'), $days) . '</span>';
}

/**
 * Format discount display
 */
function formatDiscount($coupon) {
    $type = $coupon['discount_type'] ?? 'percentage';
    $value = $coupon['discount_value'] ?? 0;
    
    switch ($type) {
        case 'percentage':
            return $value . '%';
        case 'fixed':
            return '$' . number_format($value, 0);
        case 'freebie':
            return __('FREE', 'مجاني');
        case 'deal':
            return __('DEAL', 'عرض');
        default:
            return $value;
    }
}

/**
 * Truncate text
 */
function truncate($text, $length = 150, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate breadcrumbs
 */
function breadcrumbs($items) {
    $html = '<nav class="breadcrumbs text-sm text-gray-600 dark:text-gray-400 mb-4" aria-label="Breadcrumb">';
    $html .= '<ol class="flex flex-wrap items-center gap-2" itemscope itemtype="https://schema.org/BreadcrumbList">';
    
    $position = 1;
    $total = count($items);
    
    foreach ($items as $label => $url) {
        $isLast = $position === $total;
        
        $html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center">';
        
        if ($isLast || empty($url)) {
            $html .= '<span itemprop="name" class="text-gray-900 dark:text-white font-medium">' . e($label) . '</span>';
        } else {
            $html .= '<a href="' . e($url) . '" itemprop="item" class="hover:text-indigo-600 dark:hover:text-indigo-400">';
            $html .= '<span itemprop="name">' . e($label) . '</span></a>';
            $html .= '<span class="mx-2">/</span>';
        }
        
        $html .= '<meta itemprop="position" content="' . $position . '">';
        $html .= '</li>';
        
        $position++;
    }
    
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Get asset URL
 */
function asset($path) {
    return SITE_URL . '/public/' . ltrim($path, '/');
}

/**
 * Get upload URL
 */
function upload($path) {
    return SITE_URL . '/public/uploads/' . ltrim($path, '/');
}

/**
 * Generate URL
 */
function url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Pagination helper
 */
function paginate($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav class="flex justify-center mt-8" aria-label="Pagination">';
    $html .= '<ul class="flex items-center gap-1">';
    
    // Previous
    if ($currentPage > 1) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-indigo-100 dark:hover:bg-indigo-900">&laquo;</a></li>';
    }
    
    // Pages
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        $active = $i === $currentPage ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 hover:bg-indigo-100 dark:hover:bg-indigo-900';
        $html .= '<li><a href="' . $baseUrl . '?page=' . $i . '" class="px-3 py-2 rounded-lg ' . $active . '">' . $i . '</a></li>';
    }
    
    // Next
    if ($currentPage < $totalPages) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-indigo-100 dark:hover:bg-indigo-900">&raquo;</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * Image upload handler
 */
function uploadImage($file, $folder = 'images') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File too large'];
    }
    
    // Check mime type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . strtolower($extension);
    
    // Create upload directory if not exists
    $uploadDir = UPLOADS_PATH . '/' . $folder;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move file
    $destination = $uploadDir . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $folder . '/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to upload file'];
}

/**
 * Delete uploaded file
 */
function deleteUpload($filename) {
    $path = UPLOADS_PATH . '/' . $filename;
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

/**
 * Get store logo URL or placeholder
 */
function getStoreLogo($logo, $name = '') {
    if (!empty($logo)) {
        return upload($logo);
    }
    // Return placeholder with first letter
    $letter = strtoupper(substr($name, 0, 1));
    return 'data:image/svg+xml,' . urlencode('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect fill="#6366f1" width="100" height="100"/><text fill="#fff" font-family="sans-serif" font-size="40" x="50" y="60" text-anchor="middle">' . $letter . '</text></svg>');
}

/**
 * Generate SEO meta tags
 */
function generateMetaTags($title = '', $description = '', $image = '', $type = 'website') {
    $siteTitle = getSetting('meta_title', 'Coupon & Deals');
    $fullTitle = $title ? $title . ' | ' . $siteTitle : $siteTitle;
    $desc = $description ?: getSetting('meta_description');
    $img = $image ?: getSetting('og_image');
    
    $html = '<title>' . e($fullTitle) . '</title>' . "\n";
    $html .= '<meta name="description" content="' . e($desc) . '">' . "\n";
    $html .= '<meta name="keywords" content="' . e(getSetting('meta_keywords')) . '">' . "\n";
    
    // Open Graph
    $html .= '<meta property="og:title" content="' . e($fullTitle) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . e($desc) . '">' . "\n";
    $html .= '<meta property="og:type" content="' . $type . '">' . "\n";
    $html .= '<meta property="og:url" content="' . e($_SERVER['REQUEST_URI']) . '">' . "\n";
    if ($img) {
        $html .= '<meta property="og:image" content="' . e($img) . '">' . "\n";
    }
    
    // Twitter Card
    $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $html .= '<meta name="twitter:title" content="' . e($fullTitle) . '">' . "\n";
    $html .= '<meta name="twitter:description" content="' . e($desc) . '">' . "\n";
    if ($img) {
        $html .= '<meta name="twitter:image" content="' . e($img) . '">' . "\n";
    }
    
    // Canonical URL
    $html .= '<link rel="canonical" href="' . e(SITE_URL . $_SERVER['REQUEST_URI']) . '">' . "\n";
    
    return $html;
}
