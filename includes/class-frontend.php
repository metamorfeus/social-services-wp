<?php
/**
 * Frontend Display and Shortcodes
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('ssd_directory', array($this, 'render_directory'));
        add_shortcode('ssd_provider', array($this, 'render_single_provider'));
        add_filter('single_template', array($this, 'load_single_template'));
    }
    
    /**
     * Render directory shortcode
     */
    public function render_directory($atts) {
        $atts = shortcode_atts(array(
            'view' => get_option('ssd_default_view', 'grid'),
            'per_page' => get_option('ssd_items_per_page', 12),
            'municipality' => '',
            'service' => ''
        ), $atts);
        
        ob_start();
        include SSD_PLUGIN_DIR . 'templates/directory.php';
        return ob_get_clean();
    }
    
    /**
     * Render single provider shortcode
     */
    public function render_single_provider($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);
        
        if (!$atts['id']) {
            return '';
        }
        
        $provider_id = intval($atts['id']);
        ob_start();
        include SSD_PLUGIN_DIR . 'templates/single-provider.php';
        return ob_get_clean();
    }
    
    /**
     * Load custom single template
     */
    public function load_single_template($template) {
        global $post;
        
        if ($post->post_type === 'ssd_provider') {
            $plugin_template = SSD_PLUGIN_DIR . 'templates/single-provider.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Get filtered providers
     */
    public static function get_providers($args = array()) {
        $defaults = array(
            'post_type' => 'ssd_provider',
            'posts_per_page' => get_option('ssd_items_per_page', 12),
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Handle taxonomy filters
        $tax_query = array('relation' => 'AND');
        
        if (!empty($args['municipality'])) {
            $tax_query[] = array(
                'taxonomy' => 'ssd_municipality',
                'field' => 'slug',
                'terms' => $args['municipality']
            );
        }
        
        if (!empty($args['service'])) {
            $tax_query[] = array(
                'taxonomy' => 'ssd_service_type',
                'field' => 'slug',
                'terms' => $args['service']
            );
        }
        
        if (!empty($args['target_group'])) {
            $tax_query[] = array(
                'taxonomy' => 'ssd_target_group',
                'field' => 'slug',
                'terms' => $args['target_group']
            );
        }
        
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
        
        // Handle rating filter
        if (!empty($args['min_rating'])) {
            $args['meta_query'] = array(
                array(
                    'key' => '_ssd_average_rating',
                    'value' => floatval($args['min_rating']),
                    'compare' => '>=',
                    'type' => 'DECIMAL'
                )
            );
        }
        
        // Handle search
        if (!empty($args['search'])) {
            $args['s'] = sanitize_text_field($args['search']);
        }
        
        return new WP_Query($args);
    }
    
    /**
     * Get provider card HTML
     */
    public static function get_provider_card($provider_id) {
        $provider = get_post($provider_id);
        if (!$provider) {
            return '';
        }
        
        $rating_data = SSD_Database::get_provider_rating($provider_id);
        $municipality = wp_get_post_terms($provider_id, 'ssd_municipality', array('fields' => 'names'));
        $services = wp_get_post_terms($provider_id, 'ssd_service_type', array('fields' => 'names'));
        $phone = get_post_meta($provider_id, '_ssd_phone', true);
        $address = get_post_meta($provider_id, '_ssd_address', true);
        
        ob_start();
        ?>
        <div class="ssd-provider-card" data-provider-id="<?php echo esc_attr($provider_id); ?>">
            <div class="ssd-provider-thumbnail">
                <?php if (has_post_thumbnail($provider_id)): ?>
                    <a href="<?php echo get_permalink($provider_id); ?>">
                        <?php echo get_the_post_thumbnail($provider_id, 'medium'); ?>
                    </a>
                <?php else: ?>
                    <div class="ssd-no-image">
                        <span class="dashicons dashicons-building"></span>
                    </div>
                <?php endif; ?>
                
                <?php if (get_option('ssd_enable_favorites')): ?>
                    <button class="ssd-favorite-btn" data-provider-id="<?php echo esc_attr($provider_id); ?>">
                        <span class="dashicons dashicons-heart"></span>
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="ssd-provider-content">
                <h3 class="ssd-provider-title">
                    <a href="<?php echo get_permalink($provider_id); ?>">
                        <?php echo esc_html($provider->post_title); ?>
                    </a>
                </h3>
                
                <?php if ($rating_data['count'] > 0): ?>
                    <div class="ssd-provider-rating">
                        <?php echo self::render_stars($rating_data['average']); ?>
                        <span class="ssd-rating-text">
                            <?php echo esc_html($rating_data['average']); ?> 
                            (<?php echo esc_html($rating_data['count']); ?> 
                            <?php echo $rating_data['count'] == 1 ? ssd__('review') : ssd__('reviews'); ?>)
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if ($municipality): ?>
                    <div class="ssd-provider-location">
                        <span class="dashicons dashicons-location"></span>
                        <?php echo esc_html($municipality[0]); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($services): ?>
                    <div class="ssd-provider-services">
                        <?php foreach (array_slice($services, 0, 3) as $service): ?>
                            <span class="ssd-service-badge"><?php echo esc_html($service); ?></span>
                        <?php endforeach; ?>
                        <?php if (count($services) > 3): ?>
                            <span class="ssd-more-services">+<?php echo count($services) - 3; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($address): ?>
                    <div class="ssd-provider-address">
                        <small><?php echo esc_html($address); ?></small>
                    </div>
                <?php endif; ?>
                
                <div class="ssd-provider-actions">
                    <a href="<?php echo get_permalink($provider_id); ?>" class="button ssd-view-details">
                        <?php ssd_e('view_details'); ?>
                    </a>
                    <?php if ($phone): ?>
                        <a href="tel:<?php echo esc_attr($phone); ?>" class="button ssd-call-btn">
                            <span class="dashicons dashicons-phone"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render star rating
     */
    public static function render_stars($rating) {
        $rating = floatval($rating);
        $full_stars = floor($rating);
        $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
        $empty_stars = 5 - $full_stars - $half_star;
        
        $output = '<div class="ssd-stars">';
        
        for ($i = 0; $i < $full_stars; $i++) {
            $output .= '<span class="dashicons dashicons-star-filled"></span>';
        }
        
        if ($half_star) {
            $output .= '<span class="dashicons dashicons-star-half"></span>';
        }
        
        for ($i = 0; $i < $empty_stars; $i++) {
            $output .= '<span class="dashicons dashicons-star-empty"></span>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
}
                                      