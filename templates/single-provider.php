<?php
/**
 * Template: Single Provider Details
 * 
 * Display complete provider information
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$provider_id = get_the_ID();
$rating_data = SSD_Database::get_provider_rating($provider_id);
$photos = SSD_Database::get_provider_photos($provider_id);

// Get metadata
$eik = get_post_meta($provider_id, '_ssd_eik', true);
$settlement = get_post_meta($provider_id, '_ssd_settlement', true);
$address = get_post_meta($provider_id, '_ssd_address', true);
$target_group = get_post_meta($provider_id, '_ssd_target_group', true);
$phone = get_post_meta($provider_id, '_ssd_phone', true);
$email = get_post_meta($provider_id, '_ssd_email', true);
$website = get_post_meta($provider_id, '_ssd_website', true);
$working_hours = get_post_meta($provider_id, '_ssd_working_hours', true);

// License information
$license_number = get_post_meta($provider_id, '_ssd_license_number', true);
$license_date = get_post_meta($provider_id, '_ssd_license_date', true);
$license_validity = get_post_meta($provider_id, '_ssd_license_validity', true);

$license_modified_number = get_post_meta($provider_id, '_ssd_license_modified_number', true);
$license_modified_date = get_post_meta($provider_id, '_ssd_license_modified_date', true);
$license_modified_validity = get_post_meta($provider_id, '_ssd_license_modified_validity', true);

$violations = get_post_meta($provider_id, '_ssd_violations', true);

// Get taxonomies
$municipality = wp_get_post_terms($provider_id, 'ssd_municipality');
$services = wp_get_post_terms($provider_id, 'ssd_service_type');
$target_groups = wp_get_post_terms($provider_id, 'ssd_target_group');
?>

<div class="ssd-single-provider-wrapper">
    <div class="ssd-single-container">
        
        <!-- Back Button -->
        <div class="ssd-back-navigation">
            <a href="javascript:history.back()" class="ssd-back-button">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
                <?php _e('Обратно към директорията', 'social-services-directory'); ?>
            </a>
        </div>
        
        <!-- Provider Header -->
        <div class="ssd-provider-header">
            <div class="ssd-provider-header-left">
                <!-- Main Photo -->
                <div class="ssd-provider-main-photo">
                    <?php if (has_post_thumbnail($provider_id)): ?>
                        <?php echo get_the_post_thumbnail($provider_id, 'large', array('class' => 'ssd-main-image')); ?>
                    <?php else: ?>
                        <div class="ssd-no-main-photo">
                            <span class="dashicons dashicons-building"></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Photo Gallery -->
                <?php if (!empty($photos)): ?>
                    <div class="ssd-photo-gallery-thumbs">
                        <h4><?php printf(esc_html__('Галерия (%d снимки)', 'social-services-directory'), count($photos)); ?></h4>
                        <div class="ssd-thumbs-grid">
                            <?php foreach ($photos as $photo): ?>
                                <div class="ssd-thumb-item" data-image-id="<?php echo $photo->attachment_id; ?>">
                                    <?php echo wp_get_attachment_image($photo->attachment_id, 'thumbnail'); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="ssd-provider-header-right">
                <h1 class="ssd-provider-name"><?php the_title(); ?></h1>
                
                <?php if ($rating_data['count'] > 0): ?>
                    <div class="ssd-provider-rating-large">
                        <?php echo SSD_Frontend::render_stars($rating_data['average']); ?>
                        <span class="ssd-rating-number"><?php echo esc_html($rating_data['average']); ?></span>
                        <span class="ssd-rating-count">(<?php printf(esc_html__('%d отзива', 'social-services-directory'), intval($rating_data['count'])); ?>)</span>
                    </div>
                <?php endif; ?>
                
                <?php if (!is_wp_error($municipality) && !empty($municipality)): ?>
                    <div class="ssd-provider-location-large">
                        <span class="dashicons dashicons-location"></span>
                        <strong><?php echo esc_html($municipality[0]->name); ?></strong>
                        <?php if ($settlement): ?>
                            <span>, <?php echo esc_html($settlement); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Service Badges -->
                <?php if ($services): ?>
                    <div class="ssd-provider-services-large">
                        <?php foreach ($services as $service): ?>
                            <span class="ssd-service-badge-large"><?php echo esc_html($service->name); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Quick Actions -->
                <div class="ssd-provider-quick-actions">
                    <?php if ($phone): ?>
                        <a href="tel:<?php echo esc_attr($phone); ?>" class="ssd-action-button ssd-call-button">
                            <span class="dashicons dashicons-phone"></span>
                            <?php _e('Обадете се', 'social-services-directory'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($email): ?>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="ssd-action-button ssd-email-button">
                            <span class="dashicons dashicons-email"></span>
                            <?php _e('Изпратете имейл', 'social-services-directory'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($website): ?>
                        <a href="<?php echo esc_url($website); ?>" target="_blank" class="ssd-action-button ssd-website-button">
                            <span class="dashicons dashicons-admin-links"></span>
                            <?php _e('Уебсайт', 'social-services-directory'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Provider Details Sections -->
        <div class="ssd-provider-content">
            
            <!-- Basic Information -->
            <div class="ssd-details-section">
                <h2 class="ssd-section-title"><?php _e('Основна информация', 'social-services-directory'); ?></h2>
                <div class="ssd-details-grid">
                    <div class="ssd-detail-item">
                        <span class="ssd-detail-label"><?php _e('ЕИК:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value"><?php echo $eik ? esc_html($eik) : '-'; ?></span>
                    </div>

                    <div class="ssd-detail-item">
                        <span class="ssd-detail-label"><?php _e('Община:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value">
                            <?php echo !empty($municipality) ? esc_html($municipality[0]->name) : '-'; ?>
                        </span>
                    </div>

                    <div class="ssd-detail-item">
                        <span class="ssd-detail-label"><?php _e('Населено място:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value"><?php echo $settlement ? esc_html($settlement) : '-'; ?></span>
                    </div>

                    <div class="ssd-detail-item ssd-detail-full">
                        <span class="ssd-detail-label"><?php _e('Адрес:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value"><?php echo $address ? esc_html($address) : '-'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Services and Target Groups -->
            <div class="ssd-details-section">
                <h2 class="ssd-section-title"><?php _e('Услуги и целеви групи', 'social-services-directory'); ?></h2>
                <div class="ssd-details-grid">
                    <div class="ssd-detail-item ssd-detail-full">
                        <span class="ssd-detail-label"><?php _e('Социална услуга:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value">
                            <?php if ($services): ?>
                                <?php
                                $service_names = array_map(function($s) { return $s->name; }, $services);
                                echo esc_html(implode(', ', $service_names));
                                ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="ssd-detail-item ssd-detail-full">
                        <span class="ssd-detail-label"><?php _e('Целева група:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value"><?php echo $target_group ? esc_html($target_group) : '-'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- License Information -->
            <div class="ssd-details-section">
                <h2 class="ssd-section-title"><?php _e('Лицензна информация', 'social-services-directory'); ?></h2>

                <!-- Original License -->
                <div class="ssd-license-block">
                    <h3 class="ssd-license-subtitle"><?php _e('Оригинален лиценз', 'social-services-directory'); ?></h3>
                    <div class="ssd-details-grid">
                        <div class="ssd-detail-item">
                            <span class="ssd-detail-label"><?php _e('Номер и дата на издаване:', 'social-services-directory'); ?></span>
                            <span class="ssd-detail-value"><?php echo $license_number ? esc_html($license_number) : '-'; ?></span>
                        </div>

                        <div class="ssd-detail-item">
                            <span class="ssd-detail-label"><?php _e('Дата на валидност:', 'social-services-directory'); ?></span>
                            <span class="ssd-detail-value">
                                <?php
                                if ($license_validity) {
                                    $validity_date = strtotime($license_validity);
                                    echo esc_html(date('d.m.Y', $validity_date));

                                    // Check if expired
                                    if ($validity_date < time()) {
                                        echo ' <span class="ssd-expired-badge">' . esc_html__('Изтекъл', 'social-services-directory') . '</span>';
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Modified License -->
                <?php if ($license_modified_number): ?>
                    <div class="ssd-license-block">
                        <h3 class="ssd-license-subtitle"><?php _e('Лиценз с промяна в обстоятелствата', 'social-services-directory'); ?></h3>
                        <div class="ssd-details-grid">
                            <div class="ssd-detail-item">
                                <span class="ssd-detail-label"><?php _e('Номер и дата на издаване:', 'social-services-directory'); ?></span>
                                <span class="ssd-detail-value"><?php echo esc_html($license_modified_number); ?></span>
                            </div>

                            <div class="ssd-detail-item">
                                <span class="ssd-detail-label"><?php _e('Дата на валидност:', 'social-services-directory'); ?></span>
                                <span class="ssd-detail-value">
                                    <?php
                                    if ($license_modified_validity) {
                                        $validity_date = strtotime($license_modified_validity);
                                        echo esc_html(date('d.m.Y', $validity_date));

                                        if ($validity_date < time()) {
                                            echo ' <span class="ssd-expired-badge">' . esc_html__('Изтекъл', 'social-services-directory') . '</span>';
                                        }
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Contact Information -->
            <div class="ssd-details-section">
                <h2 class="ssd-section-title"><?php _e('Контактна информация', 'social-services-directory'); ?></h2>
                <div class="ssd-details-grid">
                    <div class="ssd-detail-item">
                        <span class="ssd-detail-label"><?php _e('Телефон:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value">
                            <?php if ($phone): ?>
                                <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="ssd-detail-item">
                        <span class="ssd-detail-label"><?php _e('Имейл:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value">
                            <?php if ($email): ?>
                                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="ssd-detail-item">
                        <span class="ssd-detail-label"><?php _e('Уебсайт:', 'social-services-directory'); ?></span>
                        <span class="ssd-detail-value">
                            <?php if ($website): ?>
                                <a href="<?php echo esc_url($website); ?>" target="_blank"><?php echo esc_html($website); ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <?php if ($working_hours): ?>
                        <div class="ssd-detail-item ssd-detail-full">
                            <span class="ssd-detail-label"><?php _e('Работно време:', 'social-services-directory'); ?></span>
                            <span class="ssd-detail-value"><?php echo nl2br(esc_html($working_hours)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Violations -->
            <?php if ($violations): ?>
                <div class="ssd-details-section ssd-violations-section">
                    <h2 class="ssd-section-title"><?php _e('Установени нарушения', 'social-services-directory'); ?></h2>
                    <div class="ssd-violations-content">
                        <?php echo nl2br(esc_html($violations)); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Description -->
            <?php if (get_the_content()): ?>
                <div class="ssd-details-section">
                    <h2 class="ssd-section-title"><?php _e('Описание', 'social-services-directory'); ?></h2>
                    <div class="ssd-provider-description">
                        <?php the_content(); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Reviews Section -->
            <?php if (get_option('ssd_enable_reviews', true)): ?>
                <div class="ssd-details-section">
                    <h2 class="ssd-section-title"><?php _e('Отзиви и оценки', 'social-services-directory'); ?></h2>

                    <?php if ($rating_data['count'] > 0): ?>
                        <div class="ssd-reviews-summary">
                            <div class="ssd-reviews-overall">
                                <div class="ssd-overall-rating"><?php echo esc_html($rating_data['average']); ?></div>
                                <?php echo SSD_Frontend::render_stars($rating_data['average']); ?>
                                <div class="ssd-reviews-count"><?php printf(esc_html__('%d отзива', 'social-services-directory'), intval($rating_data['count'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="ssd-reviews-list">
                            <?php
                            global $wpdb;
                            $reviews_table = $wpdb->prefix . 'ssd_reviews';
                            $reviews = $wpdb->get_results($wpdb->prepare(
                                "SELECT r.*, u.display_name 
                                FROM $reviews_table r
                                LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
                                WHERE r.provider_id = %d AND r.status = 'approved'
                                ORDER BY r.created_at DESC
                                LIMIT 10",
                                $provider_id
                            ));
                            
                            if ($reviews):
                                foreach ($reviews as $review):
                            ?>
                                <div class="ssd-review-item">
                                    <div class="ssd-review-header">
                                        <div class="ssd-review-author"><?php echo esc_html($review->display_name); ?></div>
                                        <div class="ssd-review-rating">
                                            <?php echo SSD_Frontend::render_stars($review->rating); ?>
                                        </div>
                                        <div class="ssd-review-date">
                                            <?php echo date_i18n('d.m.Y', strtotime($review->created_at)); ?>
                                        </div>
                                    </div>
                                    <div class="ssd-review-content">
                                        <h4><?php echo esc_html($review->title); ?></h4>
                                        <p><?php echo esc_html($review->review_text); ?></p>
                                    </div>
                                </div>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    <?php else: ?>
                        <p class="ssd-no-reviews"><?php _e('Все още няма отзиви за този доставчик.', 'social-services-directory'); ?></p>
                    <?php endif; ?>
                    
                    <?php if (is_user_logged_in()): ?>
                        <button class="button ssd-write-review-btn" data-provider-id="<?php echo esc_attr($provider_id); ?>">
                            <?php _e('Напишете отзив', 'social-services-directory'); ?>
                        </button>
                    <?php else: ?>
                        <p class="ssd-login-notice">
                            <?php printf(
                                __('<a href="%s">Влезте</a>, за да напишете отзив.', 'social-services-directory'),
                                esc_url(wp_login_url(get_permalink()))
                            ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        </div>
        
    </div>
</div>

<!-- Photo Lightbox -->
<div id="ssd-lightbox" class="ssd-lightbox" style="display: none;">
    <span class="ssd-lightbox-close">&times;</span>
    <img class="ssd-lightbox-content" id="ssd-lightbox-img">
    <div class="ssd-lightbox-caption"></div>
    <button class="ssd-lightbox-prev">‹</button>
    <button class="ssd-lightbox-next">›</button>
</div>

<style>
/* Single Provider Styles */
.ssd-single-provider-wrapper {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.ssd-back-navigation {
    margin-bottom: 20px;
}

.ssd-back-button {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #0073aa;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s;
}

.ssd-back-button:hover {
    color: #005a87;
}

.ssd-provider-header {
    display: grid;
    grid-template-columns: 400px 1fr;
    gap: 40px;
    margin-bottom: 40px;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.ssd-provider-main-photo {
    width: 100%;
    height: 350px;
    border-radius: 8px;
    overflow: hidden;
    background: #f5f5f5;
    margin-bottom: 20px;
}

.ssd-provider-main-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ssd-no-main-photo {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.ssd-no-main-photo .dashicons {
    font-size: 80px;
    color: rgba(255,255,255,0.3);
}

.ssd-photo-gallery-thumbs h4 {
    margin: 0 0 15px;
    font-size: 16px;
    color: #333;
}

.ssd-thumbs-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.ssd-thumb-item {
    cursor: pointer;
    border-radius: 4px;
    overflow: hidden;
    transition: transform 0.3s;
}

.ssd-thumb-item:hover {
    transform: scale(1.05);
}

.ssd-thumb-item img {
    width: 100%;
    height: 80px;
    object-fit: cover;
}

.ssd-provider-name {
    margin: 0 0 15px;
    font-size: 32px;
    color: #333;
}

.ssd-provider-rating-large {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.ssd-rating-number {
    font-size: 24px;
    font-weight: bold;
    color: #f39c12;
}

.ssd-rating-count {
    color: #666;
}

.ssd-provider-location-large {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    font-size: 16px;
    color: #555;
}

.ssd-provider-services-large {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 25px;
}

.ssd-service-badge-large {
    background: #e8f4f8;
    color: #0073aa;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.ssd-provider-quick-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.ssd-action-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.ssd-call-button {
    background: #27ae60;
    color: #fff;
}

.ssd-call-button:hover {
    background: #229954;
    color: #fff;
}

.ssd-email-button {
    background: #3498db;
    color: #fff;
}

.ssd-email-button:hover {
    background: #2980b9;
    color: #fff;
}

.ssd-website-button {
    background: #9b59b6;
    color: #fff;
}

.ssd-website-button:hover {
    background: #8e44ad;
    color: #fff;
}

.ssd-details-section {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.ssd-section-title {
    margin: 0 0 25px;
    font-size: 24px;
    color: #333;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
}

.ssd-details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.ssd-detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.ssd-detail-full {
    grid-column: 1 / -1;
}

.ssd-detail-label {
    font-weight: 600;
    color: #555;
    font-size: 14px;
}

.ssd-detail-value {
    color: #333;
    font-size: 16px;
}

.ssd-detail-value a {
    color: #0073aa;
    text-decoration: none;
}

.ssd-detail-value a:hover {
    text-decoration: underline;
}

.ssd-license-block {
    margin-bottom: 25px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.ssd-license-subtitle {
    margin: 0 0 15px;
    font-size: 18px;
    color: #0073aa;
}

.ssd-expired-badge {
    background: #e74c3c;
    color: #fff;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.ssd-violations-section {
    border-left: 4px solid #e74c3c;
}

.ssd-violations-content {
    color: #555;
    line-height: 1.6;
}

.ssd-reviews-summary {
    margin-bottom: 30px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    text-align: center;
}

.ssd-overall-rating {
    font-size: 48px;
    font-weight: bold;
    color: #f39c12;
    margin-bottom: 10px;
}

.ssd-review-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
}

.ssd-review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.ssd-review-author {
    font-weight: bold;
    color: #333;
}

.ssd-review-date {
    color: #999;
    font-size: 14px;
}

.ssd-review-content h4 {
    margin: 0 0 10px;
    font-size: 16px;
    color: #333;
}

.ssd-review-content p {
    margin: 0;
    color: #555;
    line-height: 1.6;
}

.ssd-no-reviews {
    text-align: center;
    color: #999;
    padding: 40px 20px;
}

.ssd-write-review-btn {
    margin-top: 20px;
}

/* Lightbox */
.ssd-lightbox {
    display: none;
    position: fixed;
    z-index: 999999;
    padding-top: 60px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.9);
}

.ssd-lightbox-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 80vh;
    object-fit: contain;
}

.ssd-lightbox-close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.ssd-lightbox-prev,
.ssd-lightbox-next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    width: auto;
    margin-top: -22px;
    padding: 16px;
    color: white;
    font-weight: bold;
    font-size: 30px;
    background: rgba(0,0,0,0.5);
    border: none;
    border-radius: 4px;
    transition: 0.3s;
}

.ssd-lightbox-prev:hover,
.ssd-lightbox-next:hover {
    background: rgba(0,0,0,0.8);
}

.ssd-lightbox-prev {
    left: 20px;
}

.ssd-lightbox-next {
    right: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .ssd-provider-header {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .ssd-provider-main-photo {
        height: 250px;
    }
    
    .ssd-thumbs-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .ssd-details-grid {
        grid-template-columns: 1fr;
    }
    
    .ssd-provider-quick-actions {
        flex-direction: column;
    }
    
    .ssd-action-button {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Photo gallery lightbox
    var currentPhotoIndex = 0;
    var photos = [];
    
    $('.ssd-thumb-item').each(function(index) {
        photos.push($(this).data('image-id'));
    });
    
    $('.ssd-thumb-item').on('click', function() {
        currentPhotoIndex = $(this).index();
        showLightbox(currentPhotoIndex);
    });
    
    $('.ssd-provider-main-photo img').on('click', function() {
        currentPhotoIndex = 0;
        showLightbox(0);
    });
    
    function showLightbox(index) {
        var imageId = photos[index];
        $('#ssd-lightbox').fadeIn();
        // Load full size image
        var img = $('.ssd-thumb-item').eq(index).find('img');
        var fullSrc = img.attr('src').replace('-150x150', '');
        $('#ssd-lightbox-img').attr('src', fullSrc);
    }
    
    $('.ssd-lightbox-close').on('click', function() {
        $('#ssd-lightbox').fadeOut();
    });
    
    $('.ssd-lightbox-prev').on('click', function() {
        currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
        showLightbox(currentPhotoIndex);
    });
    
    $('.ssd-lightbox-next').on('click', function() {
        currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
        showLightbox(currentPhotoIndex);
    });
    
    $(window).on('click', function(e) {
        if (e.target.id === 'ssd-lightbox') {
            $('#ssd-lightbox').fadeOut();
        }
    });
});
</script>

<?php get_footer(); ?>
