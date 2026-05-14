<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'pupbus_pets_add_admin_user_menu');
add_action('admin_enqueue_scripts', 'pupbus_pets_enqueue_admin_assets');

function pupbus_pets_enqueue_admin_assets($hook) {
    if (strpos($hook, 'pupbus-pets') === false) {
        return;
    }
    
    wp_enqueue_style('pupbus-pets-admin-css', PUPBUS_PETS_PLUGIN_URL . 'assets/css/admin.css', array(), PUPBUS_PETS_VERSION);
    wp_enqueue_script('pupbus-pets-admin-js', PUPBUS_PETS_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PUPBUS_PETS_VERSION, true);
    wp_localize_script('pupbus-pets-admin-js', 'pupbusPetsAdmin', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pupbus_pets_admin_nonce')
    ));
}

function pupbus_pets_add_admin_user_menu() {
    add_menu_page(
        'Pupbus Pets',
        'Pupbus Pets',
        'manage_options',
        'pupbus-pets',
        'pupbus_pets_render_admin_dashboard',
        'dashicons-pets',
        30
    );
    
    add_submenu_page(
        'pupbus-pets',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'pupbus-pets',
        'pupbus_pets_render_admin_dashboard'
    );
    
    add_submenu_page(
        'pupbus-pets',
        'Registered Users',
        'Registered Users',
        'manage_options',
        'pupbus-pets-users',
        'pupbus_pets_render_users_page'
    );
    
    add_submenu_page(
        'pupbus-pets',
        'Applications',
        'Applications',
        'manage_options',
        'pupbus-pets-applications',
        'pupbus_pets_render_applications_page'
    );
}

function pupbus_pets_render_admin_dashboard() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $user_count = count_users();
    $total_users = $user_count['total_users'];
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pupbus_applications';
    $total_applications = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    
    ?>
    <div class="pupbus-pets-admin-wrap">
        <div class="pupbus-pets-admin-header">
            <h1>Pupbus Pets Dashboard</h1>
            <p class="pupbus-pets-admin-subtitle">Manage your Pupbus Pets plugin settings and shortcodes</p>
        </div>

        <div class="pupbus-pets-stats-grid">
            <div class="pupbus-pets-stat-card">
                <div class="pupbus-pets-stat-icon pupbus-pets-stat-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="pupbus-pets-stat-content">
                    <div class="pupbus-pets-stat-number"><?php echo esc_html($total_users); ?></div>
                    <div class="pupbus-pets-stat-label">Registered Users</div>
                </div>
            </div>
            
            <div class="pupbus-pets-stat-card">
                <div class="pupbus-pets-stat-icon pupbus-pets-stat-icon-orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div class="pupbus-pets-stat-content">
                    <div class="pupbus-pets-stat-number"><?php echo esc_html($total_applications); ?></div>
                    <div class="pupbus-pets-stat-label">Applications</div>
                </div>
            </div>
        </div>

        <div class="pupbus-pets-admin-card" style="margin-bottom: 24px;">
            <h2 style="font-family: Quicksand, sans-serif; font-weight: 700; font-size: 18px; color: #1a202c; margin: 0 0 20px 0;">Available Shortcodes</h2>
            <p style="font-size: 14px; color: #64748b; margin: 0 0 20px 0;">Copy and paste these shortcodes into any page or post:</p>
            
            <div class="pupbus-pets-shortcode-list">
                <div class="pupbus-pets-shortcode-item">
                    <div class="pupbus-pets-shortcode-name">Portal (Login/Register/Profile)</div>
                    <div class="pupbus-pets-shortcode-code">
                        <code>[pupbus_pets_portal]</code>
                        <button class="pupbus-pets-copy-btn" data-code="[pupbus_pets_portal]">Copy</button>
                    </div>
                </div>
                
                <div class="pupbus-pets-shortcode-item">
                    <div class="pupbus-pets-shortcode-name">Apply Now Form</div>
                    <div class="pupbus-pets-shortcode-code">
                        <code>[pupbus_pets_apply_now]</code>
                        <button class="pupbus-pets-copy-btn" data-code="[pupbus_pets_apply_now]">Copy</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="pupbus-pets-admin-card">
            <h2 style="font-family: Quicksand, sans-serif; font-weight: 700; font-size: 18px; color: #1a202c; margin: 0 0 20px 0;">Quick Links</h2>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=pupbus-pets-users')); ?>" class="pupbus-pets-btn pupbus-pets-btn-view" style="text-decoration: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    View Users
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=pupbus-pets-applications')); ?>" class="pupbus-pets-btn pupbus-pets-btn-view" style="text-decoration: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    View Applications
                </a>
            </div>
        </div>
    </div>
    <?php
}

function pupbus_pets_render_applications_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pupbus_applications';
    $applications = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC");
    ?>
    <div class="pupbus-pets-admin-wrap">
        <div class="pupbus-pets-admin-header">
            <h1>Applications</h1>
            <p class="pupbus-pets-admin-subtitle">View and manage all submitted applications</p>
        </div>

        <div class="pupbus-pets-admin-card">
            <div class="pupbus-pets-table-container">
                <table class="pupbus-pets-users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Parent Name</th>
                            <th>Email</th>
                            <th>Dog Name</th>
                            <th>Session</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)) : ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                    No applications found.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($applications as $app) : ?>
                                <tr data-application-id="<?php echo esc_attr($app->id); ?>">
                                    <td class="pupbus-pets-cell-id">#<?php echo esc_html($app->id); ?></td>
                                    <td class="pupbus-pets-cell-name">
                                        <span><?php echo esc_html($app->parent_name); ?></span>
                                    </td>
                                    <td><?php echo esc_html($app->parent_email); ?></td>
                                    <td><?php echo esc_html($app->dog_name); ?></td>
                                    <td>
                                        <span class="pupbus-pets-pet-badge" style="background: #dbeafe; color: #1d4ed8;">
                                            <?php echo esc_html(ucfirst($app->session_preference)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html(date('M j, Y', strtotime($app->submitted_at))); ?></td>
                                    <td class="pupbus-pets-cell-actions">
                                        <button class="pupbus-pets-btn pupbus-pets-btn-view" data-application-id="<?php echo esc_attr($app->id); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            View
                                        </button>
                                        <button class="pupbus-pets-btn pupbus-pets-btn-delete" data-application-id="<?php echo esc_attr($app->id); ?>" data-application-name="<?php echo esc_attr($app->parent_name); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="pupbus-pets-modal-overlay" class="pupbus-pets-modal-overlay"></div>

    <div id="pupbus-pets-view-modal" class="pupbus-pets-modal">
        <div class="pupbus-pets-modal-header">
            <h2>Application Details</h2>
            <button class="pupbus-pets-modal-close">&times;</button>
        </div>
        <div class="pupbus-pets-modal-content" id="pupbus-pets-view-modal-content">
        </div>
    </div>

    <div id="pupbus-pets-delete-modal" class="pupbus-pets-modal pupbus-pets-modal-delete">
        <div class="pupbus-pets-modal-content">
            <div class="pupbus-pets-delete-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </div>
            <h2>Delete Application?</h2>
            <p>Are you sure you want to delete this application from <strong id="pupbus-pets-delete-application-name"></strong>? This action cannot be undone.</p>
            <div class="pupbus-pets-modal-actions">
                <button class="pupbus-pets-btn pupbus-pets-btn-cancel">Cancel</button>
                <button class="pupbus-pets-btn pupbus-pets-btn-confirm-delete">Delete Application</button>
            </div>
        </div>
    </div>
    <?php
}

add_action('wp_ajax_pupbus_pets_get_user_details', 'pupbus_pets_ajax_get_user_details');
add_action('wp_ajax_pupbus_pets_delete_user', 'pupbus_pets_ajax_delete_user');
add_action('wp_ajax_pupbus_pets_get_application_details', 'pupbus_pets_ajax_get_application_details');
add_action('wp_ajax_pupbus_pets_delete_application', 'pupbus_pets_ajax_delete_application');

function pupbus_pets_ajax_get_application_details() {
    check_ajax_referer('pupbus_pets_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $app_id = isset($_POST['application_id']) ? absint($_POST['application_id']) : 0;
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pupbus_applications';
    $app = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $app_id));
    
    if (!$app) {
        wp_send_json_error('Application not found');
    }
    
    wp_send_json_success(array(
        'id' => $app->id,
        'parent_name' => $app->parent_name,
        'parent_email' => $app->parent_email,
        'parent_phone' => $app->parent_phone,
        'neighborhood' => $app->neighborhood,
        'dog_name' => $app->dog_name,
        'dog_breed' => $app->dog_breed,
        'dog_age' => $app->dog_age,
        'session_preference' => $app->session_preference,
        'pup_notes' => $app->pup_notes,
        'submitted_at' => date('Y-m-d H:i:s', strtotime($app->submitted_at))
    ));
}

function pupbus_pets_ajax_delete_application() {
    check_ajax_referer('pupbus_pets_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $app_id = isset($_POST['application_id']) ? absint($_POST['application_id']) : 0;
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pupbus_applications';
    $result = $wpdb->delete($table_name, array('id' => $app_id), array('%d'));
    
    if ($result) {
        wp_send_json_success('Application deleted successfully');
    } else {
        wp_send_json_error('Failed to delete application');
    }
}

function pupbus_pets_ajax_get_user_details() {
    check_ajax_referer('pupbus_pets_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
    $user = get_userdata($user_id);
    
    if (!$user) {
        wp_send_json_error('User not found');
    }
    
    $data = array(
        'id' => $user->ID,
        'name' => get_user_meta($user_id, 'pupbus_pets_name', true) ?: $user->display_name,
        'email' => $user->user_email,
        'phone' => get_user_meta($user_id, 'pupbus_pets_phone', true) ?: '-',
        'neighborhood' => get_user_meta($user_id, 'pupbus_pets_neighborhood', true) ?: '-',
        'home_address' => get_user_meta($user_id, 'pupbus_pets_home_address', true) ?: '-',
        'emergency_name' => get_user_meta($user_id, 'pupbus_pets_emergency_contact_name', true) ?: '-',
        'emergency_phone' => get_user_meta($user_id, 'pupbus_pets_emergency_phone', true) ?: '-',
        'registered' => date('Y-m-d H:i:s', strtotime($user->user_registered)),
        'pets' => array()
    );
    
    $pets_json = get_user_meta($user_id, 'pupbus_pets_pets_json', true);
    if ($pets_json) {
        $data['pets'] = json_decode($pets_json, true);
    }
    
    wp_send_json_success($data);
}

function pupbus_pets_ajax_delete_user() {
    check_ajax_referer('pupbus_pets_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
    
    if ($user_id === get_current_user_id()) {
        wp_send_json_error('You cannot delete your own account');
    }
    
    require_once ABSPATH . 'wp-admin/includes/user.php';
    $result = wp_delete_user($user_id);
    
    if ($result) {
        wp_send_json_success('User deleted successfully');
    } else {
        wp_send_json_error('Failed to delete user');
    }
}

function pupbus_pets_render_users_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $users = get_users(array(
        'orderby' => 'registered',
        'order' => 'DESC'
    ));
    ?>
    <div class="pupbus-pets-admin-wrap">
        <div class="pupbus-pets-admin-header">
            <h1>Registered Users</h1>
            <p class="pupbus-pets-admin-subtitle">Manage all registered users and their pets</p>
        </div>

        <div class="pupbus-pets-admin-card">
            <div class="pupbus-pets-table-container">
                <table class="pupbus-pets-users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registered</th>
                            <th>Pets</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : 
                            $pets_json = get_user_meta($user->ID, 'pupbus_pets_pets_json', true);
                            $pet_count = $pets_json ? count(json_decode($pets_json, true)) : 0;
                        ?>
                            <tr data-user-id="<?php echo esc_attr($user->ID); ?>">
                                <td class="pupbus-pets-cell-id">#<?php echo esc_html($user->ID); ?></td>
                                <td class="pupbus-pets-cell-name">
                                    <div class="pupbus-pets-avatar">
                                        <?php echo get_avatar($user->ID, 40); ?>
                                    </div>
                                    <span><?php echo esc_html(get_user_meta($user->ID, 'pupbus_pets_name', true) ?: $user->display_name); ?></span>
                                </td>
                                <td><?php echo esc_html($user->user_email); ?></td>
                                <td><?php echo esc_html(get_user_meta($user->ID, 'pupbus_pets_phone', true) ?: '-'); ?></td>
                                <td><?php echo esc_html(date('M j, Y', strtotime($user->user_registered))); ?></td>
                                <td>
                                    <span class="pupbus-pets-pet-badge"><?php echo esc_html($pet_count); ?></span>
                                </td>
                                <td class="pupbus-pets-cell-actions">
                                    <button class="pupbus-pets-btn pupbus-pets-btn-view" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        View
                                    </button>
                                    <button class="pupbus-pets-btn pupbus-pets-btn-delete" data-user-id="<?php echo esc_attr($user->ID); ?>" data-user-name="<?php echo esc_attr(get_user_meta($user->ID, 'pupbus_pets_name', true) ?: $user->display_name); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="pupbus-pets-modal-overlay" class="pupbus-pets-modal-overlay"></div>

    <div id="pupbus-pets-view-modal" class="pupbus-pets-modal">
        <div class="pupbus-pets-modal-header">
            <h2>User Details</h2>
            <button class="pupbus-pets-modal-close">&times;</button>
        </div>
        <div class="pupbus-pets-modal-content" id="pupbus-pets-view-modal-content">
        </div>
    </div>

    <div id="pupbus-pets-delete-modal" class="pupbus-pets-modal pupbus-pets-modal-delete">
        <div class="pupbus-pets-modal-content">
            <div class="pupbus-pets-delete-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </div>
            <h2>Delete User?</h2>
            <p>Are you sure you want to delete <strong id="pupbus-pets-delete-user-name"></strong>? This action cannot be undone.</p>
            <div class="pupbus-pets-modal-actions">
                <button class="pupbus-pets-btn pupbus-pets-btn-cancel">Cancel</button>
                <button class="pupbus-pets-btn pupbus-pets-btn-confirm-delete">Delete User</button>
            </div>
        </div>
    </div>
    <?php
}
