<?php
/**
 * Meta Boxes for Provider Custom Post Type
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Meta_Boxes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_ssd_provider', array($this, 'save_meta_boxes'), 10, 2);
        add_action('wp_ajax_ssd_add_photo',      array($this, 'ajax_add_photo'));
        add_action('wp_ajax_ssd_remove_photo',   array($this, 'ajax_remove_photo'));
        add_action('wp_ajax_ssd_reorder_photos', array($this, 'ajax_reorder_photos'));
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'ssd_provider_details',
            __('Provider Details', 'social-services-directory'),
            array($this, 'render_provider_details'),
            'ssd_provider',
            'normal',
            'high'
        );
        
        add_meta_box(
            'ssd_contact_info',
            __('Contact Information', 'social-services-directory'),
            array($this, 'render_contact_info'),
            'ssd_provider',
            'normal',
            'high'
        );
        
        add_meta_box(
            'ssd_license_info',
            __('License Information', 'social-services-directory'),
            array($this, 'render_license_info'),
            'ssd_provider',
            'normal',
            'default'
        );
        
        add_meta_box(
            'ssd_photo_gallery',
            __('Photo Gallery', 'social-services-directory'),
            array($this, 'render_photo_gallery'),
            'ssd_provider',
            'normal',
            'default'
        );
    }
    
    /**
     * Render provider details meta box
     */
    public function render_provider_details($post) {
        wp_nonce_field('ssd_save_meta', 'ssd_meta_nonce');
        
        $eik = get_post_meta($post->ID, '_ssd_eik', true);
        $settlement = get_post_meta($post->ID, '_ssd_settlement', true);
        $address = get_post_meta($post->ID, '_ssd_address', true);
        $target_group = get_post_meta($post->ID, '_ssd_target_group', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="ssd_eik"><?php _e('EIK (Unified ID)', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="text" id="ssd_eik" name="ssd_eik" value="<?php echo esc_attr($eik); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_settlement"><?php _e('Settlement', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="text" id="ssd_settlement" name="ssd_settlement" value="<?php echo esc_attr($settlement); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_address"><?php _e('Full Address', 'social-services-directory'); ?></label></th>
                <td>
                    <textarea id="ssd_address" name="ssd_address" rows="3" class="large-text"><?php echo esc_textarea($address); ?></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="ssd_target_group"><?php _e('Target Group Description', 'social-services-directory'); ?></label></th>
                <td>
                    <textarea id="ssd_target_group" name="ssd_target_group" rows="3" class="large-text"><?php echo esc_textarea($target_group); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Render contact info meta box
     */
    public function render_contact_info($post) {
        $phone = get_post_meta($post->ID, '_ssd_phone', true);
        $email = get_post_meta($post->ID, '_ssd_email', true);
        $website = get_post_meta($post->ID, '_ssd_website', true);
        $working_hours = get_post_meta($post->ID, '_ssd_working_hours', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="ssd_phone"><?php _e('Phone', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="tel" id="ssd_phone" name="ssd_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_email"><?php _e('Email', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="email" id="ssd_email" name="ssd_email" value="<?php echo esc_attr($email); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_website"><?php _e('Website', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="url" id="ssd_website" name="ssd_website" value="<?php echo esc_url($website); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_working_hours"><?php _e('Working Hours', 'social-services-directory'); ?></label></th>
                <td>
                    <textarea id="ssd_working_hours" name="ssd_working_hours" rows="3" class="large-text"><?php echo esc_textarea($working_hours); ?></textarea>
                    <p class="description"><?php _e('Example: Monday-Friday: 9:00-17:00', 'social-services-directory'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Render license info meta box
     */
    public function render_license_info($post) {
        $license_number = get_post_meta($post->ID, '_ssd_license_number', true);
        $license_date = get_post_meta($post->ID, '_ssd_license_date', true);
        $license_validity = get_post_meta($post->ID, '_ssd_license_validity', true);
        $violations = get_post_meta($post->ID, '_ssd_violations', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="ssd_license_number"><?php _e('License Number', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="text" id="ssd_license_number" name="ssd_license_number" value="<?php echo esc_attr($license_number); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_license_date"><?php _e('License Issue Date', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="date" id="ssd_license_date" name="ssd_license_date" value="<?php echo esc_attr($license_date); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_license_validity"><?php _e('License Valid Until', 'social-services-directory'); ?></label></th>
                <td>
                    <input type="date" id="ssd_license_validity" name="ssd_license_validity" value="<?php echo esc_attr($license_validity); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="ssd_violations"><?php _e('Violations', 'social-services-directory'); ?></label></th>
                <td>
                    <textarea id="ssd_violations" name="ssd_violations" rows="4" class="large-text"><?php echo esc_textarea($violations); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Render photo gallery meta box
     */
    public function render_photo_gallery($post) {
        $photos = SSD_Database::get_provider_photos($post->ID);
        ?>
        <div id="ssd-photo-gallery">
            <div id="ssd-photo-list">
                <?php if ($photos): ?>
                    <?php foreach ($photos as $photo): ?>
                        <div class="ssd-photo-item" data-photo-id="<?php echo esc_attr($photo->id); ?>">
                            <?php echo wp_get_attachment_image($photo->attachment_id, 'thumbnail'); ?>
                            <button type="button" class="button ssd-remove-photo"><?php _e('Remove', 'social-services-directory'); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?php _e('No photos yet.', 'social-services-directory'); ?></p>
                <?php endif; ?>
            </div>
            <button type="button" id="ssd-add-photos" class="button button-secondary">
                <?php _e('Add Photos', 'social-services-directory'); ?>
            </button>
        </div>
        <?php
    }
    
    /**
     * Save meta boxes
     */
    public function save_meta_boxes($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['ssd_meta_nonce']) || !wp_verify_nonce($_POST['ssd_meta_nonce'], 'ssd_save_meta')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save provider details
        $fields = array(
            'ssd_eik', 'ssd_settlement', 'ssd_address', 'ssd_target_group',
            'ssd_phone', 'ssd_email', 'ssd_website', 'ssd_working_hours',
            'ssd_license_number', 'ssd_license_date', 'ssd_license_validity', 'ssd_violations'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    // ── Photo AJAX ────────────────────────────────────────────────────────────

    public function ajax_add_photo() {
        check_ajax_referer('ssd_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Unauthorized.'));
            return;
        }

        $post_id       = intval($_POST['post_id'] ?? 0);
        $attachment_id = intval($_POST['attachment_id'] ?? 0);

        if (!$post_id || !$attachment_id) {
            wp_send_json_error(array('message' => 'Invalid parameters.'));
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ssd_photos';

        $max_order = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(MAX(display_order), -1) FROM $table WHERE provider_id = %d",
            $post_id
        ));

        $result = $wpdb->insert($table, array(
            'provider_id'   => $post_id,
            'attachment_id' => $attachment_id,
            'display_order' => $max_order + 1,
            'uploaded_by'   => get_current_user_id(),
        ));

        if (!$result) {
            wp_send_json_error(array('message' => 'Failed to save photo record.'));
            return;
        }

        $photo_id = $wpdb->insert_id;
        $img      = wp_get_attachment_image($attachment_id, 'thumbnail');
        $html     = '<div class="ssd-photo-item" data-photo-id="' . esc_attr($photo_id) . '">' .
                    $img .
                    '<button type="button" class="button ssd-remove-photo">' . esc_html__('Remove', 'social-services-directory') . '</button>' .
                    '</div>';

        wp_send_json_success(array('html' => $html));
    }

    public function ajax_remove_photo() {
        check_ajax_referer('ssd_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Unauthorized.'));
            return;
        }

        $photo_id = intval($_POST['photo_id'] ?? 0);
        $post_id  = intval($_POST['post_id'] ?? 0);

        global $wpdb;
        $table = $wpdb->prefix . 'ssd_photos';

        $photo = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table WHERE id = %d AND provider_id = %d",
            $photo_id,
            $post_id
        ));

        if (!$photo) {
            wp_send_json_error(array('message' => 'Photo not found.'));
            return;
        }

        $wpdb->delete($table, array('id' => $photo_id), array('%d'));
        wp_send_json_success();
    }

    public function ajax_reorder_photos() {
        check_ajax_referer('ssd_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Unauthorized.'));
            return;
        }

        $order = json_decode(stripslashes($_POST['order'] ?? '[]'), true);
        if (!is_array($order)) {
            wp_send_json_error(array('message' => 'Invalid order data.'));
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ssd_photos';

        foreach ($order as $item) {
            $wpdb->update(
                $table,
                array('display_order' => intval($item['pos'])),
                array('id' => intval($item['id'])),
                array('%d'),
                array('%d')
            );
        }

        wp_send_json_success();
    }
}
