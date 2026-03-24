<?php
/**
 * Plugin Name: Social Services Directory
 * Plugin URI: https://metamorfeus.com
 * Description: A comprehensive directory plugin for social service providers with filtering by location, services, ratings, and photo galleries.
 * Version: 1.0.0
 * Author: Metamorfeus
 * Author URI: https://metamorfeus.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: social-services-directory
 * Domain Path: /languages
 *
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SSD_VERSION', '1.0.0');
define('SSD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SSD_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Social_Services_Directory {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Load required files
     */
    private function load_dependencies() {
        // Load Bulgarian translations first
        require_once SSD_PLUGIN_DIR . 'includes/bulgarian-translations.php';
        
        require_once SSD_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once SSD_PLUGIN_DIR . 'includes/class-taxonomies.php';
        require_once SSD_PLUGIN_DIR . 'includes/class-meta-boxes.php';
        require_once SSD_PLUGIN_DIR . 'includes/class-database.php';
        require_once SSD_PLUGIN_DIR . 'includes/class-frontend.php';
        require_once SSD_PLUGIN_DIR . 'includes/class-ajax.php';
        require_once SSD_PLUGIN_DIR . 'includes/class-reviews.php';
        require_once SSD_PLUGIN_DIR . 'admin/class-admin.php';
        require_once SSD_PLUGIN_DIR . 'admin/class-import.php';
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Initialize plugin components
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('social-services-directory', false, dirname(SSD_PLUGIN_BASENAME) . '/languages');
        
        // Initialize components
        SSD_Post_Types::get_instance();
        SSD_Taxonomies::get_instance();
        SSD_Meta_Boxes::get_instance();
        SSD_Frontend::get_instance();
        SSD_Ajax::get_instance();
        SSD_Reviews::get_instance();
        SSD_Admin::get_instance();
        SSD_Import::get_instance();
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        SSD_Database::create_tables();
        
        // Register post types and taxonomies
        SSD_Post_Types::register();
        SSD_Taxonomies::register();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        $this->set_default_options();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Set default plugin options
     */
    private function set_default_options() {
        $defaults = array(
            'ssd_items_per_page' => 12,
            'ssd_enable_reviews' => true,
            'ssd_enable_photos' => true,
            'ssd_map_api_key' => '',
            'ssd_default_view' => 'grid',
            'ssd_enable_favorites' => true
        );
        
        foreach ($defaults as $key => $value) {
            if (false === get_option($key)) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // CSS
        wp_enqueue_style(
            'ssd-frontend',
            SSD_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SSD_VERSION
        );
        
        // Select2 for advanced filtering — prefer local copy, fall back to CDN
        $select2_css_local = SSD_PLUGIN_DIR . 'assets/css/select2.min.css';
        $select2_js_local  = SSD_PLUGIN_DIR . 'assets/js/select2.min.js';
        $select2_css_url   = file_exists($select2_css_local)
            ? SSD_PLUGIN_URL . 'assets/css/select2.min.css'
            : 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
        $select2_js_url    = file_exists($select2_js_local)
            ? SSD_PLUGIN_URL . 'assets/js/select2.min.js'
            : 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';

        wp_enqueue_style('select2', $select2_css_url, array(), '4.1.0');

        // JavaScript
        wp_enqueue_script('select2', $select2_js_url, array('jquery'), '4.1.0', true);
        
        wp_enqueue_script(
            'ssd-frontend',
            SSD_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery', 'select2'),
            SSD_VERSION,
            true
        );
        
        // Localize script with Bulgarian translations
        wp_localize_script('ssd-frontend', 'ssdAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssd_ajax_nonce'),
            'strings' => array(
                'loading' => ssd__('loading'),
                'noResults' => ssd__('no_providers_found'),
                'error' => ssd__('error_occurred')
            )
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'social-services') === false && 
            get_post_type() !== 'ssd_provider') {
            return;
        }
        
        wp_enqueue_style(
            'ssd-admin',
            SSD_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SSD_VERSION
        );
        
        wp_enqueue_script(
            'ssd-admin',
            SSD_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable'),
            SSD_VERSION,
            true
        );
        
        wp_localize_script('ssd-admin', 'ssdAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssd_admin_nonce')
        ));
    }
}

/**
 * Initialize the plugin
 */
function ssd_init() {
    return Social_Services_Directory::get_instance();
}

// Start the plugin
ssd_init();
