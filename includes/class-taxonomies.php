<?php
/**
 * Register Custom Taxonomies
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Taxonomies {
    
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
     * Register custom taxonomies
     */
    public static function register() {
        // Municipality Taxonomy
        register_taxonomy('ssd_municipality', array('ssd_provider'), array(
            'labels' => array(
                'name' => __('Municipalities', 'social-services-directory'),
                'singular_name' => __('Municipality', 'social-services-directory'),
                'search_items' => __('Search Municipalities', 'social-services-directory'),
                'all_items' => __('All Municipalities', 'social-services-directory'),
                'edit_item' => __('Edit Municipality', 'social-services-directory'),
                'update_item' => __('Update Municipality', 'social-services-directory'),
                'add_new_item' => __('Add New Municipality', 'social-services-directory'),
                'new_item_name' => __('New Municipality Name', 'social-services-directory'),
                'menu_name' => __('Municipalities', 'social-services-directory')
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'municipality'),
            'show_in_rest' => true
        ));
        
        // Service Type Taxonomy
        register_taxonomy('ssd_service_type', array('ssd_provider'), array(
            'labels' => array(
                'name' => __('Service Types', 'social-services-directory'),
                'singular_name' => __('Service Type', 'social-services-directory'),
                'search_items' => __('Search Service Types', 'social-services-directory'),
                'all_items' => __('All Service Types', 'social-services-directory'),
                'edit_item' => __('Edit Service Type', 'social-services-directory'),
                'update_item' => __('Update Service Type', 'social-services-directory'),
                'add_new_item' => __('Add New Service Type', 'social-services-directory'),
                'new_item_name' => __('New Service Type Name', 'social-services-directory'),
                'menu_name' => __('Service Types', 'social-services-directory')
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'service'),
            'show_in_rest' => true
        ));
        
        // Target Group Taxonomy
        register_taxonomy('ssd_target_group', array('ssd_provider'), array(
            'labels' => array(
                'name' => __('Target Groups', 'social-services-directory'),
                'singular_name' => __('Target Group', 'social-services-directory'),
                'search_items' => __('Search Target Groups', 'social-services-directory'),
                'all_items' => __('All Target Groups', 'social-services-directory'),
                'edit_item' => __('Edit Target Group', 'social-services-directory'),
                'update_item' => __('Update Target Group', 'social-services-directory'),
                'add_new_item' => __('Add New Target Group', 'social-services-directory'),
                'new_item_name' => __('New Target Group Name', 'social-services-directory'),
                'menu_name' => __('Target Groups', 'social-services-directory')
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'target-group'),
            'show_in_rest' => true
        ));
    }
}
