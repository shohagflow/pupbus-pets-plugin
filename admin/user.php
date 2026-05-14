<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'pupbus_pets_add_admin_user_menu');
add_action('admin_enqueue_scripts', 'pupbus_pets_enqueue_admin_assets');

function pupbus_pets_enqueue_admin_assets($hook) {
    if (strpos($hook, 'pupbus-pets-users') === false) {
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
    add_submenu_page(
        'pupbus-pets',
        'Registered Users',
        'Registered Users',
        'manage_options',
        'pupbus-pets-users',
        'pupbus_pets_render_users_page'
    );
}

add_action('wp_ajax_pupbus_pets_get_user_details', 'pupbus_pets_ajax_get_user_details');
add_action('wp_ajax_pupbus_pets_delete_user', 'pupbus_pets_ajax_delete_user');

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
