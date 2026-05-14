<?php
/**
 * Plugin Name: Pupbus Pets
 * Description: Front-end register, login, and profile management for Pupbus Pets.
 * Version: 1.0.1
 * Author: Pupbus
 * Text Domain: pupbus-pets
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PUPBUS_PETS_VERSION', '1.0.1');
define('PUPBUS_PETS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PUPBUS_PETS_PLUGIN_PATH', plugin_dir_path(__FILE__));

add_action('wp_enqueue_scripts', 'pupbus_pets_enqueue_assets');
function pupbus_pets_enqueue_assets() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap', array(), null);
    wp_enqueue_style('pupbus-pets-css', PUPBUS_PETS_PLUGIN_URL . 'assets/css/pupbus-pets.css', array('google-fonts'), PUPBUS_PETS_VERSION);
    wp_enqueue_script('pupbus-pets-js', PUPBUS_PETS_PLUGIN_URL . 'assets/js/pupbus-pets.js', array('jquery'), PUPBUS_PETS_VERSION, true);
}

require_once PUPBUS_PETS_PLUGIN_PATH . 'includes/login.php';
require_once PUPBUS_PETS_PLUGIN_PATH . 'includes/profile.php';
require_once PUPBUS_PETS_PLUGIN_PATH . 'includes/apply-now.php';

if (is_admin()) {
    require_once PUPBUS_PETS_PLUGIN_PATH . 'admin/user.php';
}

register_activation_hook(__FILE__, 'pupbus_pets_create_applications_table');
add_action('init', 'pupbus_pets_create_applications_table');

function pupbus_pets_create_applications_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pupbus_applications';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        parent_name varchar(255) NOT NULL,
        parent_email varchar(255) NOT NULL,
        parent_phone varchar(50) NOT NULL,
        neighborhood varchar(255) NOT NULL,
        dog_name varchar(255) NOT NULL,
        dog_breed varchar(255) NOT NULL,
        dog_age varchar(100) NOT NULL,
        session_preference varchar(50) NOT NULL,
        pup_notes text,
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
