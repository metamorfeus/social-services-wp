<?php
/**
 * Import data from CSV — AJAX-driven with progress reporting
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
        add_action('wp_ajax_ssd_import_start', array($this, 'ajax_import_start'));
        add_action('wp_ajax_ssd_import_batch', array($this, 'ajax_import_batch'));
    }

    // ── Menu ─────────────────────────────────────────────────────────────────

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

    // ── Import page HTML ─────────────────────────────────────────────────────

    public function render_import_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $nonce = wp_create_nonce('ssd_import_csv');
        ?>
        <div class="wrap">
            <h1><?php _e('Import Service Providers', 'social-services-directory'); ?></h1>

            <div class="notice notice-info">
                <p><?php _e('Import providers from a UTF-8 encoded CSV file. The import runs in batches — you can watch live progress below.', 'social-services-directory'); ?></p>
            </div>

            <!-- Upload form -->
            <div class="ssd-import-box" id="ssd-import-form-wrap">
                <h2><?php _e('Import from CSV', 'social-services-directory'); ?></h2>

                <form id="ssd-import-form" enctype="multipart/form-data">
                    <input type="hidden" id="ssd-import-nonce" value="<?php echo esc_attr($nonce); ?>">

                    <table class="form-table">
                        <tr>
                            <th><label for="csv_file"><?php _e('CSV File', 'social-services-directory'); ?></label></th>
                            <td>
                                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                                <p class="description"><?php _e('Select the CSV file exported from your database. Must be UTF-8 encoded.', 'social-services-directory'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="batch_size"><?php _e('Batch Size', 'social-services-directory'); ?></label></th>
                            <td>
                                <input type="number" id="batch_size" name="batch_size" value="50" min="1" max="500" class="small-text">
                                <p class="description"><?php _e('Records processed per request. Lower values use less memory. 50 is recommended.', 'social-services-directory'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="update_existing" name="update_existing" value="1">
                                    <?php _e('Update existing providers (matched by EIK)', 'social-services-directory'); ?>
                                </label>
                                <p class="description"><?php _e('If unchecked, providers already in the database will be skipped.', 'social-services-directory'); ?></p>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" id="ssd-start-import" class="button button-primary">
                            <?php _e('Start Import', 'social-services-directory'); ?>
                        </button>
                    </p>
                </form>
            </div>

            <!-- Progress panel (hidden until import starts) -->
            <div class="ssd-import-box" id="ssd-import-progress-wrap" style="display:none;">
                <h2><?php _e('Import Progress', 'social-services-directory'); ?></h2>

                <div class="ssd-progress-bar-wrap">
                    <div class="ssd-progress-bar" id="ssd-progress-bar"></div>
                </div>
                <p id="ssd-progress-text" class="ssd-progress-text">
                    <?php _e('Uploading file…', 'social-services-directory'); ?>
                </p>

                <div class="ssd-counters">
                    <div class="ssd-counter ssd-counter-created">
                        <span class="ssd-counter-value" id="cnt-created">0</span>
                        <span class="ssd-counter-label"><?php _e('Created', 'social-services-directory'); ?></span>
                    </div>
                    <div class="ssd-counter ssd-counter-updated">
                        <span class="ssd-counter-value" id="cnt-updated">0</span>
                        <span class="ssd-counter-label"><?php _e('Updated', 'social-services-directory'); ?></span>
                    </div>
                    <div class="ssd-counter ssd-counter-skipped">
                        <span class="ssd-counter-value" id="cnt-skipped">0</span>
                        <span class="ssd-counter-label"><?php _e('Skipped', 'social-services-directory'); ?></span>
                    </div>
                    <div class="ssd-counter ssd-counter-error">
                        <span class="ssd-counter-value" id="cnt-errors">0</span>
                        <span class="ssd-counter-label"><?php _e('Errors', 'social-services-directory'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Results panel (hidden until import completes) -->
            <div class="ssd-import-box" id="ssd-import-results-wrap" style="display:none;">
                <h2><?php _e('Import Results', 'social-services-directory'); ?></h2>

                <table class="widefat ssd-results-table" id="ssd-results-table">
                    <tbody>
                        <tr>
                            <td><?php _e('Total rows processed', 'social-services-directory'); ?></td>
                            <td><strong id="res-total">—</strong></td>
                        </tr>
                        <tr class="ssd-row-created">
                            <td><?php _e('Providers created', 'social-services-directory'); ?></td>
                            <td><strong id="res-created">—</strong></td>
                        </tr>
                        <tr class="ssd-row-updated">
                            <td><?php _e('Providers updated', 'social-services-directory'); ?></td>
                            <td><strong id="res-updated">—</strong></td>
                        </tr>
                        <tr class="ssd-row-skipped">
                            <td><?php _e('Rows skipped', 'social-services-directory'); ?></td>
                            <td><strong id="res-skipped">—</strong></td>
                        </tr>
                        <tr class="ssd-row-errors">
                            <td><?php _e('Rows with errors', 'social-services-directory'); ?></td>
                            <td><strong id="res-errors">—</strong></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Error details (hidden when no errors) -->
                <div id="ssd-error-details" style="display:none; margin-top:20px;">
                    <h3><?php _e('Error Details', 'social-services-directory'); ?></h3>
                    <p class="description"><?php _e('The following rows could not be imported:', 'social-services-directory'); ?></p>
                    <div class="ssd-error-list" id="ssd-error-list"></div>
                </div>

                <!-- Skipped details (hidden when no skips) -->
                <div id="ssd-skipped-details" style="display:none; margin-top:16px;">
                    <h3><?php _e('Skipped Rows', 'social-services-directory'); ?></h3>
                    <p class="description"><?php _e('The following rows were skipped:', 'social-services-directory'); ?></p>
                    <div class="ssd-error-list" id="ssd-skipped-list"></div>
                </div>

                <p style="margin-top:20px;">
                    <button type="button" id="ssd-import-again" class="button">
                        <?php _e('Import Another File', 'social-services-directory'); ?>
                    </button>
                </p>
            </div>

            <?php $this->render_import_instructions(); ?>
        </div>
        <?php
    }

    // ── Instructions ─────────────────────────────────────────────────────────

    private function render_import_instructions() {
        ?>
        <div class="ssd-import-instructions">
            <h3><?php _e('CSV Column Reference', 'social-services-directory'); ?></h3>
            <table class="widefat striped" style="max-width:700px;">
                <thead>
                    <tr>
                        <th><?php _e('Column name', 'social-services-directory'); ?></th>
                        <th><?php _e('Description', 'social-services-directory'); ?></th>
                        <th><?php _e('Required', 'social-services-directory'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>provider_name</code></td><td><?php _e('Provider name', 'social-services-directory'); ?></td><td><?php _e('Yes', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>eik</code></td><td><?php _e('Unified Identification Code', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>municipality</code></td><td><?php _e('Municipality name', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>settlement</code></td><td><?php _e('Settlement / city', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>address</code></td><td><?php _e('Full address', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>social_service</code></td><td><?php _e('Service type — separate multiple values with semicolons', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>target_group</code></td><td><?php _e('Target group description', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>phone</code></td><td><?php _e('Contact phone', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>email</code></td><td><?php _e('Contact email', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>license_number</code></td><td><?php _e('License number and date', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>license_validity</code></td><td><?php _e('License valid until', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>license_modified_number</code></td><td><?php _e('Modified licence number/date', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>license_modified_validity</code></td><td><?php _e('Modified licence valid until', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>license_renewed_number</code></td><td><?php _e('Renewed licence number/date', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>license_renewed_validity</code></td><td><?php _e('Renewed licence valid until', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                    <tr><td><code>violations</code></td><td><?php _e('Registered violations', 'social-services-directory'); ?></td><td><?php _e('No', 'social-services-directory'); ?></td></tr>
                </tbody>
            </table>
            <p style="margin-top:12px;">
                <strong><?php _e('Note:', 'social-services-directory'); ?></strong>
                <?php _e('The CSV must be UTF-8 encoded (with or without BOM). Large imports may take several minutes — do not close the browser window.', 'social-services-directory'); ?>
            </p>
        </div>
        <?php
    }

    // ── AJAX: Start import ────────────────────────────────────────────────────

    /**
     * Validate the uploaded file, move it to a temp location, count rows,
     * store a session transient and return metadata to the JS client.
     */
    public function ajax_import_start() {
        check_ajax_referer('ssd_import_csv', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'social-services-directory')));
            return;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $upload_error = isset($_FILES['csv_file']['error']) ? intval($_FILES['csv_file']['error']) : -1;
            wp_send_json_error(array(
                'message' => sprintf(
                    __('File upload failed (error code %d). Check your server upload limits.', 'social-services-directory'),
                    $upload_error
                ),
            ));
            return;
        }

        $tmp = $_FILES['csv_file']['tmp_name'];
        if (!is_uploaded_file($tmp)) {
            wp_send_json_error(array('message' => __('Invalid file upload.', 'social-services-directory')));
            return;
        }

        // Move file to a persistent location for batch processing
        $upload_dir = wp_upload_dir();
        $session_id = wp_generate_uuid4();
        $dest       = $upload_dir['basedir'] . '/ssd-import-' . $session_id . '.csv';

        if (!move_uploaded_file($tmp, $dest)) {
            wp_send_json_error(array('message' => __('Could not save the uploaded file. Check directory permissions.', 'social-services-directory')));
            return;
        }

        // Read and validate headers
        $handle = fopen($dest, 'r');
        if (!$handle) {
            @unlink($dest);
            wp_send_json_error(array('message' => __('Could not open the CSV file for reading.', 'social-services-directory')));
            return;
        }

        // Strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = fgetcsv($handle);
        if (!$headers || !in_array('provider_name', $headers, true)) {
            fclose($handle);
            @unlink($dest);
            wp_send_json_error(array('message' => __('Invalid CSV format. The file must have a "provider_name" column in the header row.', 'social-services-directory')));
            return;
        }

        // Count data rows
        $total = 0;
        while (fgetcsv($handle) !== false) {
            $total++;
        }
        fclose($handle);

        if ($total === 0) {
            @unlink($dest);
            wp_send_json_error(array('message' => __('The CSV file contains no data rows.', 'social-services-directory')));
            return;
        }

        $batch_size      = max(1, intval($_POST['batch_size'] ?? 50));
        $update_existing = !empty($_POST['update_existing']);

        // Persist session data for subsequent batch requests
        set_transient('ssd_import_' . $session_id, array(
            'file'            => $dest,
            'headers'         => $headers,
            'total'           => $total,
            'batch_size'      => $batch_size,
            'update_existing' => $update_existing,
            'has_bom'         => ($bom === "\xEF\xBB\xBF"),
        ), 2 * HOUR_IN_SECONDS);

        wp_send_json_success(array(
            'session_id' => $session_id,
            'total'      => $total,
            'batch_size' => $batch_size,
        ));
    }

    // ── AJAX: Process one batch ───────────────────────────────────────────────

    /**
     * Process a single batch of rows and return per-batch statistics.
     * The JS accumulates totals across batches.
     */
    public function ajax_import_batch() {
        check_ajax_referer('ssd_import_csv', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'social-services-directory')));
            return;
        }

        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $offset     = max(0, intval($_POST['offset'] ?? 0));

        if (empty($session_id)) {
            wp_send_json_error(array('message' => __('Missing session ID.', 'social-services-directory')));
            return;
        }

        $session = get_transient('ssd_import_' . $session_id);
        if (!$session) {
            wp_send_json_error(array('message' => __('Import session not found or expired. Please start the import again.', 'social-services-directory')));
            return;
        }

        $file            = $session['file'];
        $headers         = $session['headers'];
        $batch_size      = $session['batch_size'];
        $update_existing = $session['update_existing'];
        $total           = $session['total'];
        $has_bom         = $session['has_bom'];

        if (!file_exists($file)) {
            delete_transient('ssd_import_' . $session_id);
            wp_send_json_error(array('message' => __('Import file not found on disk. The server may have cleaned it up.', 'social-services-directory')));
            return;
        }

        set_time_limit(120);

        $handle = fopen($file, 'r');

        // Skip BOM
        if ($has_bom) {
            fread($handle, 3);
        }

        fgetcsv($handle); // skip header row

        // Seek to the correct offset
        $current = 0;
        while ($current < $offset && fgetcsv($handle) !== false) {
            $current++;
        }

        $imported  = 0;
        $updated   = 0;
        $skipped   = 0;
        $errors    = array();
        $skips     = array();
        $processed = 0;

        while ($processed < $batch_size && ($raw = fgetcsv($handle)) !== false) {
            $row_num = $offset + $processed + 2; // +1 header, +1 for 1-based display

            // Column count mismatch → skip silently
            if (count($raw) !== count($headers)) {
                $skipped++;
                $skips[] = array(
                    'row'     => $row_num,
                    'name'    => '',
                    'message' => sprintf(
                        __('Column count mismatch: expected %d, got %d.', 'social-services-directory'),
                        count($headers),
                        count($raw)
                    ),
                );
                $processed++;
                continue;
            }

            $row    = array_combine($headers, $raw);
            $result = $this->import_provider($row, $update_existing);

            switch ($result['action']) {
                case 'created':
                    $imported++;
                    break;
                case 'updated':
                    $updated++;
                    break;
                case 'skipped':
                    $skipped++;
                    $skips[] = array(
                        'row'     => $row_num,
                        'name'    => $result['provider_name'],
                        'message' => $result['message'],
                    );
                    break;
                case 'error':
                    $errors[] = array(
                        'row'     => $row_num,
                        'name'    => $result['provider_name'],
                        'message' => $result['message'],
                    );
                    break;
            }

            $processed++;
        }

        fclose($handle);

        $new_offset = $offset + $processed;
        $done       = $new_offset >= $total;

        // Cleanup temp file when fully done
        if ($done) {
            @unlink($file);
            delete_transient('ssd_import_' . $session_id);
        }

        wp_send_json_success(array(
            'processed' => $processed,
            'imported'  => $imported,
            'updated'   => $updated,
            'skipped'   => $skipped,
            'errors'    => $errors,
            'skips'     => $skips,
            'done'      => $done,
            'offset'    => $new_offset,
            'total'     => $total,
        ));
    }

    // ── Import single provider ────────────────────────────────────────────────

    /**
     * Returns array with keys: action (created|updated|skipped|error),
     * provider_name, provider_id (on success), message (on skip/error).
     */
    private function import_provider($data, $update_existing = false) {
        $provider_name = sanitize_text_field($data['provider_name'] ?? '');
        $eik           = sanitize_text_field($data['eik'] ?? '');

        if (empty($provider_name)) {
            return array(
                'action'        => 'skipped',
                'provider_name' => '',
                'message'       => __('Provider name is empty.', 'social-services-directory'),
            );
        }

        // Look up existing provider by EIK
        $existing = null;
        if ($eik) {
            $existing_query = new WP_Query(array(
                'post_type'      => 'ssd_provider',
                'posts_per_page' => 1,
                'no_found_rows'  => true,
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

        // Skip if exists and update is disabled
        if ($existing && !$update_existing) {
            return array(
                'action'        => 'skipped',
                'provider_name' => $provider_name,
                'message'       => $eik
                    ? sprintf(__('Already exists (EIK: %s). Update existing is disabled.', 'social-services-directory'), $eik)
                    : __('Already exists. Update existing is disabled.', 'social-services-directory'),
            );
        }

        $post_data = array(
            'post_title'   => $provider_name,
            'post_type'    => 'ssd_provider',
            'post_status'  => 'publish',
            'post_content' => sanitize_textarea_field($data['description'] ?? ''),
        );

        if ($existing) {
            $post_data['ID'] = $existing->ID;
            $provider_id     = wp_update_post($post_data, true);
            $action          = 'updated';
        } else {
            $provider_id = wp_insert_post($post_data, true);
            $action      = 'created';
        }

        if (is_wp_error($provider_id)) {
            return array(
                'action'        => 'error',
                'provider_name' => $provider_name,
                'message'       => $provider_id->get_error_message(),
            );
        }

        // Save all metadata
        $meta_fields = array(
            'eik', 'settlement', 'address', 'target_group',
            'phone', 'email', 'website', 'working_hours',
            'license_number', 'license_date', 'license_validity',
            'license_modified_number', 'license_modified_validity',
            'license_renewed_number', 'license_renewed_validity',
            'violations',
        );
        foreach ($meta_fields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                update_post_meta($provider_id, '_ssd_' . $field, sanitize_textarea_field($data[$field]));
            }
        }

        // Municipality taxonomy
        if (!empty($data['municipality'])) {
            $muni_name    = sanitize_text_field(trim($data['municipality']));
            $municipality = term_exists($muni_name, 'ssd_municipality');
            if (!$municipality) {
                $municipality = wp_insert_term($muni_name, 'ssd_municipality');
            }
            if (!is_wp_error($municipality)) {
                wp_set_post_terms($provider_id, array(intval($municipality['term_id'])), 'ssd_municipality');
            }
        }

        // Service type taxonomy — supports semicolon-separated multiple values
        if (!empty($data['social_service'])) {
            $service_names = array_filter(array_map('trim', explode(';', $data['social_service'])));
            $term_ids      = array();
            foreach ($service_names as $svc_name) {
                $svc_name = sanitize_text_field($svc_name);
                $term     = term_exists($svc_name, 'ssd_service_type');
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
            'action'        => $action,
            'provider_id'   => $provider_id,
            'provider_name' => $provider_name,
        );
    }
}
