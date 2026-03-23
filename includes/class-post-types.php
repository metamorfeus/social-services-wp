<?php
/**
 * Register Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Post_Types {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register'));
    }
    
    /**
     * Register custom post types
     */
    public static function register() {
        // Service Provider Post Type
        register_post_type('ssd_provider', array(
            'labels' => array(
                'name' => __('Service Providers', 'social-services-directory'),
                'singular_name' => __('Service Provider', 'social-services-directory'),
                'add_new' => __('Add New Provider', 'social-services-directory'),
                'add_new_item' => __('Add New Service Provider', 'social-services-directory'),
                'edit_item' => __('Edit Service Provider', 'social-services-directory'),
                'new_item' => __('New Service Provider', 'social-services-directory'),
                'view_item' => __('View Service Provider', 'social-services-directory'),
                'search_items' => __('Search Providers', 'social-services-directory'),
                'not_found' => __('No providers found', 'social-services-directory'),
                'not_found_in_trash' => __('No providers found in trash', 'social-services-directory')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'providers'),
            'menu_icon' => 'dashicons-groups',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest' => true,
            'menu_position' => 5,
            'capability_type' => 'post'
        ));
        
        // Review Post Type (for user reviews)
        register_post_type('ssd_review', array(
            'labels' => array(
                'name' => __('Reviews', 'social-services-directory'),
                'singular_name' => __('Review', 'social-services-directory')
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=ssd_provider',
            'supports' => array('title', 'editor', 'author'),
            'capability_type' => 'post'
        ));
    }
}
