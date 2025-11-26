<?php
/**
 * Page Controller
 * Handles static pages
 */

require_once APP_PATH . '/controllers/BaseController.php';

class PageController extends BaseController {
    
    /**
     * Show static page
     */
    public function show($slug) {
        $page = $this->db->fetch(
            "SELECT * FROM pages WHERE slug = :slug AND is_active = 1",
            ['slug' => $slug]
        );
        
        if (!$page) {
            http_response_code(404);
            $this->view('frontend/404');
            return;
        }
        
        $this->view('frontend/page', [
            'pageTitle' => $page['seo_title'] ?: getLocalizedField($page, 'title'),
            'pageDescription' => $page['seo_description'],
            'page' => $page
        ]);
    }
}
