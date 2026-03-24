<?php
/**
 * Import data from MySQL or CSV
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Import {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_ssd_import_csv', array($this, 'handle_csv_import'));
    }
    
    /**
     * Add import menu page
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=ssd_provider',
            __('Import Data', 'social-services-directory'),
            __('Import Data', 'social-services-directory'),
            'manage_options',
            'ssd-import',
            array($this, 'render_import_page')
        );
    }
    
    /**
     * Render import page
     */
    public function render_import_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Import Service Providers', 'social-services-directory'); ?></h1>
            
            <div class="notice notice-info">
                <p><?php _e('Import providers from the cleaned CSV file generated during MySQL preparation.', 'social-services-directory'); ?></p>
            </div>
            
            <div class="ssd-import-methods">
                <!-- CSV Import -->
                <div class="ssd-import-box">
                    <h2><?php _e('Import from CSV', 'social-services-directory'); ?></h2>
                    <p><?php _e('Upload the cleaned_social_services_data.csv file.', 'social-services-directory'); ?></p>
                    
                    <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                        <?php wp_nonce_field('ssd_import_csv', 'ssd_import_nonce'); ?>
                        <input type="hidden" name="action" value="ssd_import_csv">
                        
                        <table class="form-table">
                            <tr>
                                <th><label for="csv_file"><?php _e('CSV File', 'social-services-directory'); ?></label></th>
                                <td>
                                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                                    <p class="description"><?php _e('Select the CSV file exported from your database.', 'social-services-directory'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="batch_size"><?php _e('Batch Size', 'social-services-directory'); ?></label></th>
                                <td>
                                    <input type="number" id="batch_size" name="batch_size" value="50" min="1" max="500" class="small-text">
                                    <p class="description"><?php _e('Number of records to import at once. Lower values use less memory.', 'social-services-directory'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="update_existing" value="1">
                                        <?php _e('Update existing providers (match by EIK)', 'social-services-directory'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" class="button button-primary" value="<?php _e('Start Import', 'social-services-directory'); ?>">
                        </p>
                    </form>
                </div>
            </div>
            
            <?php $this->render_import_instructions(); ?>
        </div>
        
        <style>
        .ssd-import-box {
            background: #fff;
            border: 1px solid #ccd0d4;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .ssd-import-box h2 {
            margin-top: 0;
        }
        .ssd-import-instructions {
            background: #f9f9f9;
            border-left: 4px solid #0073aa;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .ssd-import-instructions h3 {
            margin-top: 0;
        }
        .ssd-import-instructions ol {
            margin-left: 20px;
        }
        .ssd-import-instructions code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
        }
        </style>
        <?php
    }
    
    /**
     * Render import instructions
     */
    private function render_import_instructions() {
        ?>
        <div class="ssd-import-instructions">
            <h3><?php _e('CSV Import Instructions', 'social-services-directory'); ?></h3>
            <ol>
                <li><?php _e('Prepare your CSV file with the following columns:', 'social-services-directory'); ?>
                    <ul>
                        <li><code>provider_name</code> - <?php _e('Provider name (required)', 'social-services-directory'); ?></li>
                        <li><code>eik</code> - <?php _e('Unified Identification Code', 'social-services-directory'); ?></li>
                        <li><code>municipality</code> - <?php _e('Municipality name', 'social-services-directory'); ?></li>
                        <li><code>settlement</code> - <?php _e('Settlement/city', 'social-services-directory'); ?></li>
                        <li><code>address</code> - <?php _e('Full address', 'social-services-directory'); ?></li>
                        <li><code>social_service</code> - <?php _e('Service type — use semicolons to separate multiple values', 'social-services-directory'); ?></li>
                        <li><code>target_group</code> - <?php _e('Target group description', 'social-services-directory'); ?></li>
                        <li><code>phone</code> - <?php _e('Contact phone', 'social-services-directory'); ?></li>
                        <li><code>email</code> - <?php _e('Contact email', 'social-services-directory'); ?></li>
                        <li><code>license_number</code> - <?php _e('License number and issue date', 'social-services-directory'); ?></li>
                        <li><code>license_validity</code> - <?php _e('License valid until date', 'social-services-directory'); ?></li>
                        <li><code>license_modified_number</code> - <?php _e('Modified license number and date (optional)', 'social-services-directory'); ?></li>
                        <li><code>license_modified_validity</code> - <?php _e('Modified license valid until (optional)', 'social-services-directory'); ?></li>
                        <li><code>license_renewed_number</code> - <?php _e('Renewed license number and date (optional)', 'social-services-directory'); ?></li>
                        <li><code>license_renewed_validity</code> - <?php _e('Renewed license valid until (optional)', 'social-services-directory'); ?></li>
                        <li><code>violations</code> - <?php _e('Registered violations (optional)', 'social-services-directory'); ?></li>
                    </ul>
                </li>
                <li><?php _e('Ensure the CSV file is UTF-8 encoded (especially for Bulgarian Cyrillic text).', 'social-services-directory'); ?></li>
                <li><?php _e('Upload the file using the form above.', 'social-services-directory'); ?></li>
                <li><?php _e('The import process will:', 'social-services-directory'); ?>
                    <ul>
                        <li><?php _e('Create or update service providers', 'social-services-directory'); ?></li>
                        <li><?php _e('Automatically create municipality and service type terms', 'social-services-directory'); ?></li>
                        <li><?php _e('Associate providers with appropriate taxonomies', 'social-services-directory'); ?></li>
                        <li><?php _e('Save all metadata fields', 'social-services-directory'); ?></li>
                    </ul>
                </li>
            </ol>
            
            <p><strong><?php _e('Note:', 'social-services-directory'); ?></strong> 
            <?php _e('Large imports may take several minutes. Do not close the browser window during import.', 'social-services-directory'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Handle CSV import
     */
    public function handle_csv_import() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized access', 'social-services-directory'));
        }
        
        check_admin_referer('ssd_import_csv', 'ssd_import_nonce');
        
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            wp_die(__('Failed to upload CSV file', 'social-services-directory'));
        }
        
        $file = $_FILES['csv_file']['tmp_name'];

        if (!is_uploaded_file($file)) {
            wp_die(__('Invalid file upload.', 'social-services-directory'));
        }

        $batch_size = intval($_POST['batch_size'] ?? 50);
        $update_existing = isset($_POST['update_existing']);

        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $handle = fopen($file, 'r');
        if (!$handle) {
            wp_die(__('Failed to read CSV file', 'social-services-directory'));
        }
        
        // Get headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            wp_die(__('Invalid CSV format', 'social-services-directory'));
        }
        
        $imported = 0;
        $updated = 0;
        $errors = array();
        
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($headers)) {
                continue;
            }
            
            $row = array_combine($headers, $data);
            
            try {
                $result = $this->import_provider($row, $update_existing);
                if ($result['action'] === 'created') {
                    $imported++;
                } else if ($result['action'] === 'updated') {
                    $updated++;
                }
            } catch (Exception $e) {
                $errors[] = sprintf(__('Row error: %s', 'social-services-directory'), $e->getMessage());
            }
        }
        
        fclose($handle);
        
        $message = sprintf(
            __('Import completed: %d created, %d updated, %d errors', 'social-services-directory'),
            $imported,
            $updated,
            count($errors)
        );
        
        wp_redirect(add_query_arg(array(
            'post_type' => 'ssd_provider',
            'page' => 'ssd-import',
            'imported' => $imported,
            'updated' => $updated,
            'errors' => count($errors)
        ), admin_url('edit.php')));
        exit;
    }
    
    /**
     * Import single provider
     */
    private function import_provider($data, $update_existing = false) {
        $provider_name = sanitize_text_field($data['provider_name'] ?? '');
        $eik = sanitize_text_field($data['eik'] ?? '');
        
        if (empty($provider_name)) {
            throw new Exception('Provider name is required');
        }
        
        // Check if provider exists (by EIK)
        $existing = null;
        if ($eik && $update_existing) {
            $existing_query = new WP_Query(array(
                'post_type'      => 'ssd_provider',
                'posts_per_page' => 1,
                'meta_query'     => array(
                    array(
                        'key'     => '_ssd_eik',
                        'value'   => $eik,
                        'compare' => '=',
                    ),
                ),
            ));
            
            if ($existing_query->have_posts()) {
                $existing = $existing_query->posts[0];
            }
        }
        
        $post_data = array(
            'post_title' => $provider_name,
            'post_type' => 'ssd_provider',
            'post_status' => 'publish',
            'post_content' => sanitize_textarea_field($data['description'] ?? '')
        );
        
        if ($existing) {
            $post_data['ID'] = $existing->ID;
            $provider_id = wp_update_post($post_data);
            $action = 'updated';
        } else {
            $provider_id = wp_insert_post($post_data);
            $action = 'created';
        }
        
        if (is_wp_error($provider_id)) {
            throw new Exception($provider_id->get_error_message());
        }
        
        // Save metadata (single-value fields)
        $meta_fields = array(
            'eik', 'settlement', 'address', 'target_group',
            'phone', 'email', 'website', 'working_hours',
            'license_number', 'license_date', 'license_validity',
            'license_modified_number', 'license_modified_validity',
            'license_renewed_number', 'license_renewed_validity',
            'violations'
        );

        foreach ($meta_fields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                update_post_meta($provider_id, '_ssd_' . $field, sanitize_textarea_field($data[$field]));
            }
        }

        // Set municipality taxonomy
        if (!empty($data['municipality'])) {
            $muni_name = sanitize_text_field(trim($data['municipality']));
            $municipality = term_exists($muni_name, 'ssd_municipality');
            if (!$municipality) {
                $municipality = wp_insert_term($muni_name, 'ssd_municipality');
            }
            if (!is_wp_error($municipality)) {
                wp_set_post_terms($provider_id, array(intval($municipality['term_id'])), 'ssd_municipality');
            }
        }

        // Set service type taxonomy — supports semicolon-separated multiple values
        if (!empty($data['social_service'])) {
            $service_names = array_filter(array_map('trim', explode(';', $data['social_service'])));
            $term_ids = array();
            foreach ($service_names as $svc_name) {
                $svc_name = sanitize_text_field($svc_name);
                $term = term_exists($svc_name, 'ssd_service_type');
                if (!$term) {
                    $term = wp_insert_term($svc_name, 'ssd_service_type');
                }
                if (!is_wp_error($term)) {
                    $term_ids[] = intval($term['term_id']);
                }
            }
            if (!empty($term_ids)) {
                wp_set_post_terms($provider_id, $term_ids, 'ssd_service_type');
            }
        }
        
        return array(
            'provider_id' => $provider_id,
            'action' => $action
        );
    }
}
