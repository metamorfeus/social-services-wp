<?php
/**
 * Reviews Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Reviews {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Add admin menu for reviews
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=ssd_provider',
            __('Reviews', 'social-services-directory'),
            __('Reviews', 'social-services-directory'),
            'manage_options',
            'ssd-reviews',
            array($this, 'render_reviews_page')
        );
    }
    
    /**
     * Render reviews admin page
     */
    public function render_reviews_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'ssd_reviews';
        
        // Handle bulk actions
        if (isset($_POST['action']) && $_POST['action'] !== '-1') {
            $this->handle_bulk_action();
        }
        
        // Get reviews
        $allowed_statuses = array('pending', 'approved', 'spam');
        $status = (isset($_GET['status']) && in_array($_GET['status'], $allowed_statuses, true)) ? $_GET['status'] : 'pending';
        $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $per_page = 20;
        $offset = ($paged - 1) * $per_page;
        
        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, p.post_title as provider_name, u.display_name as user_name
            FROM $table r
            LEFT JOIN {$wpdb->posts} p ON r.provider_id = p.ID
            LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
            WHERE r.status = %s
            ORDER BY r.created_at DESC
            LIMIT %d OFFSET %d",
            $status,
            $per_page,
            $offset
        ));
        
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE status = %s",
            $status
        ));
        
        $pending_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'pending'));
        $approved_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'approved'));
        $spam_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'spam'));
        
        ?>
        <div class="wrap">
            <h1><?php _e('Manage Reviews', 'social-services-directory'); ?></h1>
            
            <ul class="subsubsub">
                <li>
                    <a href="?post_type=ssd_provider&page=ssd-reviews&status=pending" <?php echo $status === 'pending' ? 'class="current"' : ''; ?>>
                        <?php _e('Pending', 'social-services-directory'); ?> 
                        <span class="count">(<?php echo intval($pending_count); ?>)</span>
                    </a> |
                </li>
                <li>
                    <a href="?post_type=ssd_provider&page=ssd-reviews&status=approved" <?php echo $status === 'approved' ? 'class="current"' : ''; ?>>
                        <?php _e('Approved', 'social-services-directory'); ?>
                        <span class="count">(<?php echo intval($approved_count); ?>)</span>
                    </a> |
                </li>
                <li>
                    <a href="?post_type=ssd_provider&page=ssd-reviews&status=spam" <?php echo $status === 'spam' ? 'class="current"' : ''; ?>>
                        <?php _e('Spam', 'social-services-directory'); ?>
                        <span class="count">(<?php echo intval($spam_count); ?>)</span>
                    </a>
                </li>
            </ul>
            
            <form method="post">
                <?php wp_nonce_field('ssd_reviews_bulk', 'ssd_reviews_nonce'); ?>
                
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <select name="action">
                            <option value="-1"><?php _e('Bulk Actions', 'social-services-directory'); ?></option>
                            <option value="approve"><?php _e('Approve', 'social-services-directory'); ?></option>
                            <option value="spam"><?php _e('Mark as Spam', 'social-services-directory'); ?></option>
                            <option value="delete"><?php _e('Delete', 'social-services-directory'); ?></option>
                        </select>
                        <input type="submit" class="button action" value="<?php _e('Apply', 'social-services-directory'); ?>">
                    </div>
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="check-column"><input type="checkbox" id="cb-select-all"></td>
                            <th><?php _e('Provider', 'social-services-directory'); ?></th>
                            <th><?php _e('Rating', 'social-services-directory'); ?></th>
                            <th><?php _e('Review', 'social-services-directory'); ?></th>
                            <th><?php _e('User', 'social-services-directory'); ?></th>
                            <th><?php _e('Date', 'social-services-directory'); ?></th>
                            <th><?php _e('Actions', 'social-services-directory'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reviews): ?>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="review_ids[]" value="<?php echo esc_attr($review->id); ?>">
                                    </th>
                                    <td>
                                        <strong><?php echo esc_html($review->provider_name); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo SSD_Frontend::render_stars($review->rating); ?>
                                        <span><?php echo esc_html($review->rating); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($review->title); ?></strong>
                                        <p><?php echo esc_html(wp_trim_words($review->review_text, 20)); ?></p>
                                    </td>
                                    <td><?php echo esc_html($review->user_name); ?></td>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($review->created_at))); ?></td>
                                    <td>
                                        <?php if ($status === 'pending'): ?>
                                            <a href="<?php echo wp_nonce_url(admin_url('edit.php?post_type=ssd_provider&page=ssd-reviews&action=approve&review_id=' . $review->id), 'approve_review'); ?>" class="button button-small">
                                                <?php _e('Approve', 'social-services-directory'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo wp_nonce_url(admin_url('edit.php?post_type=ssd_provider&page=ssd-reviews&action=delete&review_id=' . $review->id), 'delete_review'); ?>" class="button button-small" onclick="return confirm('<?php _e('Are you sure?', 'social-services-directory'); ?>')">
                                            <?php _e('Delete', 'social-services-directory'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7"><?php _e('No reviews found.', 'social-services-directory'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if ($total > $per_page): ?>
                    <div class="tablenav bottom">
                        <?php
                        $total_pages = ceil($total / $per_page);
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'current' => $paged,
                            'total' => $total_pages
                        ));
                        ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Handle bulk actions
     */
    private function handle_bulk_action() {
        if (!isset($_POST['ssd_reviews_nonce']) || !wp_verify_nonce($_POST['ssd_reviews_nonce'], 'ssd_reviews_bulk')) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $action = sanitize_text_field($_POST['action']);
        $review_ids = array_map('intval', $_POST['review_ids'] ?? array());
        
        if (empty($review_ids)) {
            return;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'ssd_reviews';
        
        foreach ($review_ids as $review_id) {
            switch ($action) {
                case 'approve':
                    $wpdb->update($table, array('status' => 'approved'), array('id' => $review_id));
                    break;
                case 'spam':
                    $wpdb->update($table, array('status' => 'spam'), array('id' => $review_id));
                    break;
                case 'delete':
                    $wpdb->delete($table, array('id' => $review_id));
                    break;
            }
        }
        
        wp_redirect(add_query_arg(array('updated' => count($review_ids))));
        exit;
    }
}
