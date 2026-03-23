<?php
/**
 * Template: Directory Listing
 * 
 * Display the service providers directory with filters
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get all municipalities
$municipalities = get_terms(array(
    'taxonomy' => 'ssd_municipality',
    'hide_empty' => false
));

// Get all services
$services = get_terms(array(
    'taxonomy' => 'ssd_service_type',
    'hide_empty' => false
));

// Get initial providers
$providers = SSD_Frontend::get_providers(array(
    'paged' => 1
));
?>

<div class="ssd-directory-wrapper">
    <!-- Filters Section -->
    <div class="ssd-filters">
        <div class="ssd-filters-header">
            <h2><?php _e('Find Service Providers', 'social-services-directory'); ?></h2>
            <button class="ssd-toggle-filters" type="button">
                <span class="dashicons dashicons-filter"></span>
                <?php _e('Filters', 'social-services-directory'); ?>
            </button>
        </div>
        
        <form id="ssd-filter-form" class="ssd-filter-form">
            <div class="ssd-filter-row">
                <!-- Search -->
                <div class="ssd-filter-field">
                    <label for="ssd-search"><?php _e('Search', 'social-services-directory'); ?></label>
                    <input type="text" id="ssd-search" name="search" placeholder="<?php _e('Search providers...', 'social-services-directory'); ?>">
                </div>
                
                <!-- Municipality Filter -->
                <div class="ssd-filter-field">
                    <label for="ssd-municipality"><?php _e('Municipality', 'social-services-directory'); ?></label>
                    <select id="ssd-municipality" name="municipality" class="ssd-select2">
                        <option value=""><?php _e('All Municipalities', 'social-services-directory'); ?></option>
                        <?php foreach ($municipalities as $municipality): ?>
                            <option value="<?php echo esc_attr($municipality->slug); ?>">
                                <?php echo esc_html($municipality->name); ?> (<?php echo $municipality->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Service Filter -->
                <div class="ssd-filter-field">
                    <label for="ssd-service"><?php _e('Service Type', 'social-services-directory'); ?></label>
                    <select id="ssd-service" name="service" class="ssd-select2">
                        <option value=""><?php _e('All Services', 'social-services-directory'); ?></option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo esc_attr($service->slug); ?>">
                                <?php echo esc_html($service->name); ?> (<?php echo $service->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Rating Filter -->
                <div class="ssd-filter-field">
                    <label for="ssd-rating"><?php _e('Minimum Rating', 'social-services-directory'); ?></label>
                    <select id="ssd-rating" name="min_rating">
                        <option value=""><?php _e('Any Rating', 'social-services-directory'); ?></option>
                        <option value="4">4+ <?php _e('stars', 'social-services-directory'); ?></option>
                        <option value="3">3+ <?php _e('stars', 'social-services-directory'); ?></option>
                        <option value="2">2+ <?php _e('stars', 'social-services-directory'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="ssd-filter-actions">
                <button type="submit" class="button button-primary ssd-apply-filters">
                    <?php _e('Apply Filters', 'social-services-directory'); ?>
                </button>
                <button type="reset" class="button ssd-reset-filters">
                    <?php _e('Reset', 'social-services-directory'); ?>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Results Header -->
    <div class="ssd-results-header">
        <div class="ssd-results-info">
            <span class="ssd-results-count">
                <?php printf(__('Found %d providers', 'social-services-directory'), $providers->found_posts); ?>
            </span>
        </div>
        
        <div class="ssd-results-controls">
            <!-- Sort By -->
            <select id="ssd-sort" class="ssd-sort-select">
                <option value="title-ASC"><?php _e('Name (A-Z)', 'social-services-directory'); ?></option>
                <option value="title-DESC"><?php _e('Name (Z-A)', 'social-services-directory'); ?></option>
                <option value="date-DESC"><?php _e('Newest First', 'social-services-directory'); ?></option>
                <option value="rating-DESC"><?php _e('Highest Rated', 'social-services-directory'); ?></option>
            </select>
            
            <!-- View Toggle -->
            <div class="ssd-view-toggle">
                <button class="ssd-view-btn active" data-view="grid">
                    <span class="dashicons dashicons-grid-view"></span>
                </button>
                <button class="ssd-view-btn" data-view="list">
                    <span class="dashicons dashicons-list-view"></span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="ssd-loading-overlay" style="display: none;">
        <div class="ssd-spinner"></div>
    </div>
    
    <!-- Providers Grid/List -->
    <div id="ssd-providers-container" class="ssd-providers-grid" data-view="grid">
        <?php if ($providers->have_posts()): ?>
            <?php while ($providers->have_posts()): $providers->the_post(); ?>
                <?php echo SSD_Frontend::get_provider_card(get_the_ID()); ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else: ?>
            <div class="ssd-no-results">
                <span class="dashicons dashicons-info"></span>
                <p><?php _e('No service providers found.', 'social-services-directory'); ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($providers->max_num_pages > 1): ?>
        <div class="ssd-pagination">
            <button class="button ssd-load-more" data-page="1" data-max="<?php echo $providers->max_num_pages; ?>">
                <?php _e('Load More', 'social-services-directory'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize Select2
    $('.ssd-select2').select2({
        width: '100%',
        placeholder: function() {
            return $(this).find('option:first').text();
        }
    });
    
    // Toggle filters on mobile
    $('.ssd-toggle-filters').on('click', function() {
        $('.ssd-filter-form').slideToggle();
    });
    
    // Filter form submission
    $('#ssd-filter-form').on('submit', function(e) {
        e.preventDefault();
        filterProviders(1);
    });
    
    // Reset filters
    $('.ssd-reset-filters').on('click', function() {
        $('#ssd-filter-form')[0].reset();
        $('.ssd-select2').val('').trigger('change');
        filterProviders(1);
    });
    
    // Sort change
    $('#ssd-sort').on('change', function() {
        filterProviders(1);
    });
    
    // View toggle
    $('.ssd-view-btn').on('click', function() {
        const view = $(this).data('view');
        $('.ssd-view-btn').removeClass('active');
        $(this).addClass('active');
        
        const $container = $('#ssd-providers-container');
        $container.removeClass('ssd-providers-grid ssd-providers-list');
        $container.addClass('ssd-providers-' + view);
        $container.attr('data-view', view);
    });
    
    // Load more
    $(document).on('click', '.ssd-load-more', function() {
        const $btn = $(this);
        const page = parseInt($btn.data('page')) + 1;
        const max = parseInt($btn.data('max'));
        
        if (page > max) {
            return;
        }
        
        $btn.prop('disabled', true).text(ssdAjax.strings.loading);
        
        $.ajax({
            url: ssdAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ssd_load_more',
                nonce: ssdAjax.nonce,
                paged: page
            },
            success: function(response) {
                if (response.success) {
                    $('#ssd-providers-container').append(response.data.html);
                    $btn.data('page', page);
                    
                    if (!response.data.has_more) {
                        $btn.hide();
                    } else {
                        $btn.prop('disabled', false).text('<?php _e('Load More', 'social-services-directory'); ?>');
                    }
                }
            }
        });
    });
    
    // Favorite toggle
    $(document).on('click', '.ssd-favorite-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const providerId = $btn.data('provider-id');
        
        $.ajax({
            url: ssdAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ssd_toggle_favorite',
                nonce: ssdAjax.nonce,
                provider_id: providerId
            },
            success: function(response) {
                if (response.success) {
                    $btn.toggleClass('favorited');
                }
            }
        });
    });
    
    // Filter providers function
    function filterProviders(page) {
        const $form = $('#ssd-filter-form');
        const $container = $('#ssd-providers-container');
        const $overlay = $('.ssd-loading-overlay');
        const $resultsCount = $('.ssd-results-count');
        
        const sortValue = $('#ssd-sort').val().split('-');
        
        const data = {
            action: 'ssd_filter_providers',
            nonce: ssdAjax.nonce,
            search: $('#ssd-search').val(),
            municipality: $('#ssd-municipality').val(),
            service: $('#ssd-service').val(),
            min_rating: $('#ssd-rating').val(),
            orderby: sortValue[0],
            order: sortValue[1],
            paged: page
        };
        
        $overlay.show();
        
        $.ajax({
            url: ssdAjax.ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                $overlay.hide();
                
                if (response.success) {
                    $container.html(response.data.html);
                    $resultsCount.text('<?php _e('Found', 'social-services-directory'); ?> ' + response.data.found + ' <?php _e('providers', 'social-services-directory'); ?>');
                    
                    // Update load more button
                    if (response.data.max_pages > 1) {
                        $('.ssd-load-more').data('page', 1).data('max', response.data.max_pages).show();
                    } else {
                        $('.ssd-load-more').hide();
                    }
                } else {
                    $container.html('<div class="ssd-no-results"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $overlay.hide();
                alert(ssdAjax.strings.error);
            }
        });
    }
});
</script>
