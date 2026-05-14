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

require_once PUPBUS_PETS_PLUGIN_PATH . 'includes/login.php';
require_once PUPBUS_PETS_PLUGIN_PATH . 'includes/profile.php';
