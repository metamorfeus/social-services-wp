<?php
/**
 * Database Schema and Tables
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Database {
    
    /**
     * Create custom database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Reviews table
        $reviews_table = $wpdb->prefix . 'ssd_reviews';
        $reviews_sql = "CREATE TABLE IF NOT EXISTS $reviews_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            provider_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            rating decimal(2,1) NOT NULL,
            title varchar(255) NOT NULL,
            review_text text NOT NULL,
            helpful_count int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY provider_id (provider_id),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Photos table
        $photos_table = $wpdb->prefix . 'ssd_photos';
        $photos_sql = "CREATE TABLE IF NOT EXISTS $photos_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            provider_id bigint(20) NOT NULL,
            attachment_id bigint(20) NOT NULL,
            title varchar(255) DEFAULT NULL,
            description text DEFAULT NULL,
            display_order int(11) DEFAULT 0,
            uploaded_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY provider_id (provider_id),
            KEY display_order (display_order)
        ) $charset_collate;";
        
        // Provider metadata table
        $meta_table = $wpdb->prefix . 'ssd_provider_meta';
        $meta_sql = "CREATE TABLE IF NOT EXISTS $meta_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            provider_id bigint(20) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext,
            PRIMARY KEY (id),
            KEY provider_id (provider_id),
            KEY meta_key (meta_key)
        ) $charset_collate;";
        
        // Favorites table
        $favorites_table = $wpdb->prefix . 'ssd_favorites';
        $favorites_sql = "CREATE TABLE IF NOT EXISTS $favorites_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            provider_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_provider (user_id, provider_id),
            KEY user_id (user_id),
            KEY provider_id (provider_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($reviews_sql);
        dbDelta($photos_sql);
        dbDelta($meta_sql);
        dbDelta($favorites_sql);
    }
    
    /**
     * Get provider average rating
     */
    public static function get_provider_rating($provider_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ssd_reviews';
        
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                AVG(rating) as average_rating,
                COUNT(*) as review_count
            FROM $table
            WHERE provider_id = %d AND status = 'approved'",
            $provider_id
        ));
        
        return array(
            'average' => $result ? round($result->average_rating, 1) : 0,
            'count' => $result ? intval($result->review_count) : 0
        );
    }
    
    /**
     * Get provider photos
     */
    public static function get_provider_photos($provider_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ssd_photos';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE provider_id = %d 
            ORDER BY display_order ASC",
            $provider_id
        ));
    }
    
    /**
     * Check if user favorited provider
     */
    public static function is_favorited($user_id, $provider_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ssd_favorites';
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table 
            WHERE user_id = %d AND provider_id = %d",
            $user_id,
            $provider_id
        ));
        
        return $count > 0;
    }
}
