<?php
/**
 * AJAX Handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Ajax {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Public AJAX actions
        add_action('wp_ajax_ssd_filter_providers', array($this, 'filter_providers'));
        add_action('wp_ajax_nopriv_ssd_filter_providers', array($this, 'filter_providers'));
        
        add_action('wp_ajax_ssd_toggle_favorite', array($this, 'toggle_favorite'));
        
        add_action('wp_ajax_ssd_submit_review', array($this, 'submit_review'));
        
        add_action('wp_ajax_ssd_load_more', array($this, 'load_more'));
        add_action('wp_ajax_nopriv_ssd_load_more', array($this, 'load_more'));
    }
    
    /**
     * Filter providers AJAX
     */
    public function filter_providers() {
        check_ajax_referer('ssd_ajax_nonce', 'nonce');
        
        $filters = array(
            'municipality' => sanitize_text_field($_POST['municipality'] ?? ''),
            'service' => sanitize_text_field($_POST['service'] ?? ''),
            'target_group' => sanitize_text_field($_POST['target_group'] ?? ''),
            'min_rating' => floatval($_POST['min_rating'] ?? 0),
            'search' => sanitize_text_field($_POST['search'] ?? ''),
            'orderby' => sanitize_text_field($_POST['orderby'] ?? 'title'),
            'order' => sanitize_text_field($_POST['order'] ?? 'ASC'),
            'paged' => intval($_POST['paged'] ?? 1)
        );
        
        $query = SSD_Frontend::get_providers($filters);
        
        if (!$query->have_posts()) {
            wp_send_json_error(array(
                'message' => __('No providers found matching your criteria.', 'social-services-directory')
            ));
        }
        
        $html = '';
        while ($query->have_posts()) {
            $query->the_post();
            $html .= SSD_Frontend::get_provider_card(get_the_ID());
        }
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'html' => $html,
            'found' => $query->found_posts,
            'max_pages' => $query->max_num_pages,
            'current_page' => $filters['paged']
        ));
    }
    
    /**
     * Toggle favorite AJAX
     */
    public function toggle_favorite() {
        check_ajax_referer('ssd_ajax_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('You must be logged in to save favorites.', 'social-services-directory')
            ));
        }
        
        $user_id = get_current_user_id();
        $provider_id = intval($_POST['provider_id']);
        
        global $wpdb;
        $table = $wpdb->prefix . 'ssd_favorites';
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND provider_id = %d",
            $user_id,
            $provider_id
        ));
        
        if ($exists) {
            // Remove favorite
            $wpdb->delete($table, array(
                'user_id' => $user_id,
                'provider_id' => $provider_id
            ));
            
            wp_send_json_success(array(
                'action' => 'removed',
                'message' => __('Removed from favorites.', 'social-services-directory')
            ));
        } else {
            // Add favorite
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'provider_id' => $provider_id
            ));
            
            wp_send_json_success(array(
                'action' => 'added',
                'message' => __('Added to favorites.', 'social-services-directory')
            ));
        }
    }
    
    /**
     * Submit review AJAX
     */
    public function submit_review() {
        check_ajax_referer('ssd_ajax_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('You must be logged in to submit a review.', 'social-services-directory')
            ));
        }
        
        $user_id = get_current_user_id();
        $provider_id = intval($_POST['provider_id']);

        // Validate rating immediately before any further processing
        $rating_raw = floatval($_POST['rating'] ?? 0);
        if ($rating_raw < 1 || $rating_raw > 5) {
            wp_send_json_error(array(
                'message' => __('Invalid rating.', 'social-services-directory')
            ));
            return;
        }
        $rating = $rating_raw;

        $title = sanitize_text_field($_POST['title'] ?? '');
        $review_text = sanitize_textarea_field($_POST['review_text'] ?? '');

        if (empty($title) || empty($review_text)) {
            wp_send_json_error(array(
                'message' => __('Title and review text are required.', 'social-services-directory')
            ));
        }
        
        // Check if user already reviewed
        global $wpdb;
        $table = $wpdb->prefix . 'ssd_reviews';
        
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND provider_id = %d",
            $user_id,
            $provider_id
        ));
        
        if ($existing) {
            wp_send_json_error(array(
                'message' => __('You have already reviewed this provider.', 'social-services-directory')
            ));
        }
        
        // Insert review
        $result = $wpdb->insert($table, array(
            'provider_id' => $provider_id,
            'user_id' => $user_id,
            'rating' => $rating,
            'title' => $title,
            'review_text' => $review_text,
            'status' => get_option('ssd_auto_approve_reviews', false) ? 'approved' : 'pending'
        ));
        
        if ($result) {
            // Update provider average rating
            $this->update_provider_rating($provider_id);
            
            wp_send_json_success(array(
                'message' => __('Thank you for your review!', 'social-services-directory')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to submit review. Please try again.', 'social-services-directory')
            ));
        }
    }
    
    /**
     * Load more providers AJAX
     */
    public function load_more() {
        check_ajax_referer('ssd_ajax_nonce', 'nonce');
        
        $paged = intval($_POST['paged'] ?? 1);
        
        $query = SSD_Frontend::get_providers(array(
            'paged' => $paged
        ));
        
        if (!$query->have_posts()) {
            wp_send_json_error(array(
                'message' => __('No more providers to load.', 'social-services-directory')
            ));
        }
        
        $html = '';
        while ($query->have_posts()) {
            $query->the_post();
            $html .= SSD_Frontend::get_provider_card(get_the_ID());
        }
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $paged < $query->max_num_pages
        ));
    }
    
    /**
     * Update provider average rating
     */
    private function update_provider_rating($provider_id) {
        $rating_data = SSD_Database::get_provider_rating($provider_id);
        update_post_meta($provider_id, '_ssd_average_rating', $rating_data['average']);
        update_post_meta($provider_id, '_ssd_review_count', $rating_data['count']);
    }
}
