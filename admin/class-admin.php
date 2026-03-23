<?php
/**
 * Admin Settings and Pages
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=ssd_provider',
            __('Settings', 'social-services-directory'),
            __('Settings', 'social-services-directory'),
            'manage_options',
            'ssd-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('ssd_settings_group', 'ssd_items_per_page');
        register_setting('ssd_settings_group', 'ssd_enable_reviews');
        register_setting('ssd_settings_group', 'ssd_enable_photos');
        register_setting('ssd_settings_group', 'ssd_enable_favorites');
        register_setting('ssd_settings_group', 'ssd_default_view');
        register_setting('ssd_settings_group', 'ssd_auto_approve_reviews');
        register_setting('ssd_settings_group', 'ssd_map_api_key');
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Save settings
        if (isset($_POST['ssd_save_settings'])) {
            check_admin_referer('ssd_settings_nonce');
            
            update_option('ssd_items_per_page', intval($_POST['ssd_items_per_page']));
            update_option('ssd_enable_reviews', isset($_POST['ssd_enable_reviews']));
            update_option('ssd_enable_photos', isset($_POST['ssd_enable_photos']));
            update_option('ssd_enable_favorites', isset($_POST['ssd_enable_favorites']));
            update_option('ssd_default_view', sanitize_text_field($_POST['ssd_default_view']));
            update_option('ssd_auto_approve_reviews', isset($_POST['ssd_auto_approve_reviews']));
            update_option('ssd_map_api_key', sanitize_text_field($_POST['ssd_map_api_key']));
            
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'social-services-directory') . '</p></div>';
        }
        
        $items_per_page = get_option('ssd_items_per_page', 12);
        $enable_reviews = get_option('ssd_enable_reviews', true);
        $enable_photos = get_option('ssd_enable_photos', true);
        $enable_favorites = get_option('ssd_enable_favorites', true);
        $default_view = get_option('ssd_default_view', 'grid');
        $auto_approve = get_option('ssd_auto_approve_reviews', false);
        $map_api_key = get_option('ssd_map_api_key', '');
        
        ?>
        <div class="wrap">
            <h1><?php _e('Social Services Directory - Settings', 'social-services-directory'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('ssd_settings_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ssd_items_per_page"><?php _e('Items Per Page', 'social-services-directory'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="ssd_items_per_page" name="ssd_items_per_page" value="<?php echo esc_attr($items_per_page); ?>" min="1" max="100" class="small-text">
                            <p class="description"><?php _e('Number of providers to display per page', 'social-services-directory'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ssd_default_view"><?php _e('Default View', 'social-services-directory'); ?></label>
                        </th>
                        <td>
                            <select id="ssd_default_view" name="ssd_default_view">
                                <option value="grid" <?php selected($default_view, 'grid'); ?>><?php _e('Grid View', 'social-services-directory'); ?></option>
                                <option value="list" <?php selected($default_view, 'list'); ?>><?php _e('List View', 'social-services-directory'); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Enable Features', 'social-services-directory'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="ssd_enable_reviews" <?php checked($enable_reviews); ?>>
                                    <?php _e('Enable Reviews', 'social-services-directory'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="ssd_enable_photos" <?php checked($enable_photos); ?>>
                                    <?php _e('Enable Photo Gallery', 'social-services-directory'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="ssd_enable_favorites" <?php checked($enable_favorites); ?>>
                                    <?php _e('Enable Favorites', 'social-services-directory'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="ssd_auto_approve_reviews" <?php checked($auto_approve); ?>>
                                    <?php _e('Auto-approve Reviews', 'social-services-directory'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ssd_map_api_key"><?php _e('Google Maps API Key', 'social-services-directory'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="ssd_map_api_key" name="ssd_map_api_key" value="<?php echo esc_attr($map_api_key); ?>" class="regular-text">
                            <p class="description">
                                <?php _e('Optional: Add Google Maps API key to display provider locations on a map.', 'social-services-directory'); ?>
                                <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><?php _e('Get API Key', 'social-services-directory'); ?></a>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="ssd_save_settings" class="button button-primary" value="<?php _e('Save Settings', 'social-services-directory'); ?>">
                </p>
            </form>
            
            <hr>
            
            <h2><?php _e('Shortcodes', 'social-services-directory'); ?></h2>
            <p><?php _e('Use the following shortcodes to display the directory on your pages:', 'social-services-directory'); ?></p>
            
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Shortcode', 'social-services-directory'); ?></th>
                        <th><?php _e('Description', 'social-services-directory'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[ssd_directory]</code></td>
                        <td><?php _e('Display the full directory with filters', 'social-services-directory'); ?></td>
                    </tr>
                    <tr>
                        <td><code>[ssd_directory view="list"]</code></td>
                        <td><?php _e('Display directory in list view', 'social-services-directory'); ?></td>
                    </tr>
                    <tr>
                        <td><code>[ssd_directory municipality="sofia"]</code></td>
                        <td><?php _e('Filter by specific municipality', 'social-services-directory'); ?></td>
                    </tr>
                    <tr>
                        <td><code>[ssd_provider id="123"]</code></td>
                        <td><?php _e('Display a specific provider by ID', 'social-services-directory'); ?></td>
                    </tr>
                </tbody>
            </table>
            
            <hr>
            
            <h2><?php _e('Statistics', 'social-services-directory'); ?></h2>
            <?php
            $provider_count = wp_count_posts('ssd_provider')->publish;
            $municipality_count = wp_count_terms(array('taxonomy' => 'ssd_municipality'));
            $service_count = wp_count_terms(array('taxonomy' => 'ssd_service_type'));
            
            global $wpdb;
            $review_table = $wpdb->prefix . 'ssd_reviews';
            $review_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $review_table WHERE status = %s", 'approved'));
            ?>
            
            <table class="widefat">
                <tbody>
                    <tr>
                        <td><strong><?php _e('Total Providers', 'social-services-directory'); ?></strong></td>
                        <td><?php echo number_format_i18n($provider_count); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Municipalities', 'social-services-directory'); ?></strong></td>
                        <td><?php echo number_format_i18n($municipality_count); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Service Types', 'social-services-directory'); ?></strong></td>
                        <td><?php echo number_format_i18n($service_count); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Approved Reviews', 'social-services-directory'); ?></strong></td>
                        <td><?php echo number_format_i18n($review_count); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
