<?php
/**
 * Subscribe Controller
 * Handles email subscriptions
 */

require_once APP_PATH . '/controllers/BaseController.php';

class SubscribeController extends BaseController {
    
    /**
     * Store new subscriber
     */
    public function store() {
        $this->validateCSRF();
        
        $email = filter_var($this->post('email'), FILTER_VALIDATE_EMAIL);
        $name = $this->post('name', '');
        
        if (!$email) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json(['error' => __('Invalid email address', 'عنوان بريد إلكتروني غير صالح')], 400);
            }
            setFlash('error', __('Invalid email address', 'عنوان بريد إلكتروني غير صالح'));
            redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
        }
        
        // Check if already subscribed
        $existing = $this->db->fetch(
            "SELECT id, is_active FROM subscribers WHERE email = :email",
            ['email' => $email]
        );
        
        if ($existing) {
            if ($existing['is_active']) {
                $message = __('You are already subscribed!', 'أنت مشترك بالفعل!');
            } else {
                // Reactivate subscription
                $this->db->update('subscribers', ['is_active' => 1, 'unsubscribed_at' => null], 'id = :id', ['id' => $existing['id']]);
                $message = __('Your subscription has been reactivated!', 'تم إعادة تفعيل اشتراكك!');
            }
        } else {
            // Create new subscription
            $this->db->insert('subscribers', [
                'email' => $email,
                'name' => $name,
                'is_active' => 1
            ]);
            $message = __('Thank you for subscribing!', 'شكراً لاشتراكك!');
        }
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->json(['success' => true, 'message' => $message]);
        }
        
        setFlash('success', $message);
        redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
    }
}
