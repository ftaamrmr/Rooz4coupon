<?php
/**
 * Admin Logout
 */

require_once __DIR__ . '/../config/config.php';

logout();
setFlash('success', 'You have been logged out successfully.');
redirect(ADMIN_URL . '/login.php');
