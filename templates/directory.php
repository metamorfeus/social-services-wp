<?php
/**
 * Template for Provider Directory Listing (Bulgarian Version)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get filter parameters
$municipality = isset($_GET['municipality']) ? sanitize_text_field($_GET['municipality']) : '';
$service = isset($_GET['service']) ? sanitize_text_field($_GET['service']) : '';
$target_group = isset($_GET['target_group']) ? sanitize_text_field($_GET['target_group']) : '';
$min_rating = isset($_GET['rating']) ? floatval($_GET['rating']) : 0;
$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$view_mode = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : $atts['view'];
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// Build query args
$query_args = array(
    'paged' => $paged,
    'posts_per_page' => $atts['per_page']
);

if ($municipality) {
    $query_args['municipality'] = $municipality;
}

if ($service) {
    $query_args['service'] = $service;
}

if ($target_group) {
    $query_args['target_group'] = $target_group;
}

if ($min_rating > 0) {
    $query_args['min_rating'] = $min_rating;
}

if ($search) {
    $query_args['search'] = $search;
}

// Get providers
$providers_query = SSD_Frontend::get_providers($query_args);

// Get all municipalities and services for filters
$municipalities = get_terms(array(
    'taxonomy' => 'ssd_municipality',
    'hide_empty' => true
));

$services = get_terms(array(
    'taxonomy' => 'ssd_service_type',
    'hide_empty' => true
));

$target_groups = get_terms(array(
    'taxonomy' => 'ssd_target_group',
    'hide_empty' => true
));
?>

<div class="ssd-directory-wrap" id="ssd-directory">
    
    <!-- Header Section -->
    <div class="ssd-directory-header">
        <h1 class="ssd-directory-title"><?php ssd_e('find_service_providers'); ?></h1>
        
        <!-- Search Bar -->
        <div class="ssd-search-section">
            <form class="ssd-search-form" method="get">
                <input type="text" 
                       name="search" 
                       class="ssd-search-input" 
                       placeholder="<?php ssd_e('search_placeholder'); ?>"
                       value="<?php echo esc_attr($search); ?>">
                <button type="submit" class="ssd-search-btn">
                    <span class="dashicons dashicons-search"></span>
                    <?php ssd_e('search'); ?>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Filters Section -->
    <div class="ssd-filters-section">
        <div class="ssd-filters-header">
            <h3><?php ssd_e('filter_by'); ?></h3>
            <button class="ssd-toggle-filters">
                <span class="dashicons dashicons-filter"></span>
            </button>
        </div>
        
        <div class="ssd-filters-content">
            <form class="ssd-filters-form" method="get">
                
                <!-- Municipality Filter -->
                <div class="ssd-filter-group">
                    <label><?php ssd_e('municipality'); ?></label>
                    <select name="municipality" class="ssd-filter-select">
                        <option value=""><?php ssd_e('all_municipalities'); ?></option>
                        <?php foreach ($municipalities as $term): ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" 
                                    <?php selected($municipality, $term->slug); ?>>
                                <?php echo esc_html($term->name); ?> (<?php echo $term->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Service Type Filter -->
                <div class="ssd-filter-group">
                    <label><?php ssd_e('services_offered'); ?></label>
                    <select name="service" class="ssd-filter-select">
                        <option value=""><?php ssd_e('all_services'); ?></option>
                        <?php foreach ($services as $term): ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" 
                                    <?php selected($service, $term->slug); ?>>
                                <?php echo esc_html($term->name); ?> (<?php echo $term->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Target Group Filter -->
                <div class="ssd-filter-group">
                    <label><?php ssd_e('target_groups'); ?></label>
                    <select name="target_group" class="ssd-filter-select">
                        <option value=""><?php ssd_e('all_target_groups'); ?></option>
                        <?php foreach ($target_groups as $term): ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" 
                                    <?php selected($target_group, $term->slug); ?>>
                                <?php echo esc_html($term->name); ?> (<?php echo $term->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Rating Filter -->
                <div class="ssd-filter-group">
                    <label><?php ssd_e('minimum_rating'); ?></label>
                    <select name="rating" class="ssd-filter-select">
                        <option value=""><?php ssd_e('any_rating'); ?></option>
                        <option value="4" <?php selected($min_rating, 4); ?>>4+ ⭐</option>
                        <option value="3" <?php selected($min_rating, 3); ?>>3+ ⭐</option>
                        <option value="2" <?php selected($min_rating, 2); ?>>2+ ⭐</option>
                        <option value="1" <?php selected($min_rating, 1); ?>>1+ ⭐</option>
                    </select>
                </div>
                
                <!-- Filter Actions -->
                <div class="ssd-filter-actions">
                    <button type="submit" class="button button-primary">
                        <?php ssd_e('apply_filters'); ?>
                    </button>
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="button">
                        <?php ssd_e('reset_filters'); ?>
                    </a>
                </div>
                
            </form>
        </div>
    </div>
    
    <!-- Results Header -->
    <div class="ssd-results-header">
        <div class="ssd-results-count">
            <?php 
            printf(
                ssd__('showing_results') . ' <strong>%s</strong> ' . ssd__('of') . ' <strong>%s</strong> ' . ssd__('results'),
                number_format_i18n(($providers_query->post_count)),
                number_format_i18n($providers_query->found_posts)
            );
            ?>
        </div>
        
        <div class="ssd-view-controls">
            <span><?php ssd_e('view_mode'); ?>:</span>
            <button class="ssd-view-toggle <?php echo $view_mode === 'grid' ? 'active' : ''; ?>" 
                    data-view="grid">
                <span class="dashicons dashicons-grid-view"></span>
                <?php ssd_e('grid_view'); ?>
            </button>
            <button class="ssd-view-toggle <?php echo $view_mode === 'list' ? 'active' : ''; ?>" 
                    data-view="list">
                <span class="dashicons dashicons-list-view"></span>
                <?php ssd_e('list_view'); ?>
            </button>
        </div>
        
        <div class="ssd-sort-controls">
            <label><?php ssd_e('sort_by'); ?>:</label>
            <select class="ssd-sort-select">
                <option value="name-asc"><?php ssd_e('name_asc'); ?></option>
                <option value="name-desc"><?php ssd_e('name_desc'); ?></option>
                <option value="rating-high"><?php ssd_e('rating_high'); ?></option>
                <option value="rating-low"><?php ssd_e('rating_low'); ?></option>
                <option value="newest"><?php ssd_e('newest'); ?></option>
            </select>
        </div>
    </div>
    
    <!-- Providers Grid/List -->
    <div class="ssd-providers-container" data-view="<?php echo esc_attr($view_mode); ?>">
        <?php if ($providers_query->have_posts()): ?>
            <div class="ssd-providers-grid">
                <?php while ($providers_query->have_posts()): $providers_query->the_post(); ?>
                    <?php echo SSD_Frontend::get_provider_card(get_the_ID()); ?>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($providers_query->max_num_pages > 1): ?>
                <div class="ssd-pagination">
                    <?php
                    echo paginate_links(array(
                        'total' => $providers_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '&laquo; ' . ssd__('previous_page'),
                        'next_text' => ssd__('next_page') . ' &raquo;',
                        'type' => 'list'
                    ));
                    ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="ssd-no-results">
                <span class="dashicons dashicons-warning"></span>
                <h3><?php ssd_e('no_providers_found'); ?></h3>
                <p><?php ssd_e('try_different_filters'); ?></p>
                <a href="<?php echo esc_url(get_permalink()); ?>" class="button">
                    <?php ssd_e('reset_filters'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php wp_reset_postdata(); ?>
</div>
