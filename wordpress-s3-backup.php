<?php

/*
Plugin Name: Wordpress S3 Backup
Plugin URI:  https://github.com/aeryaguzov/wordpress-s3-backup
Description: Backup wordpress files and upload them to Amazon S3 Storage
Version:     dev-master
Author:      Andrey Ryaguzov
Author URI:  https://github.com/aeryaguzov
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined('ABSPATH') || die();

add_action('admin_menu', 'wps3backup_add_menu');
add_action('admin_init', 'wps3backup_register_settings');

/**
 * Add "S3 Backup" sub_menu for "Tools" menu
 */
function wps3backup_add_menu() {
    add_management_page(
        'Wordpress S3 Backup Settings',
        'S3 Backup',
        'manage_options',
        's3-backup-settings',
        'wps3backup_settings_page'
    );
}

/**
 * Register plugin settings
 */
function wps3backup_register_settings() {
    register_setting('wordpress_s3_backup', 'wps3backup_s3_region');
    register_setting('wordpress_s3_backup', 'wps3backup_s3_bucket');
    register_setting('wordpress_s3_backup', 'wps3backup_s3_api_key');
    register_setting('wordpress_s3_backup', 'wps3backup_s3_api_secret');
}

/**
 * Render plugin settings page
 * Trying to render like a simple template
 */
function wps3backup_settings_page() {
    $tpl_vars = array(
        'wps3backup_s3_region_value' => get_option('wps3backup_s3_region', ''),
        'wps3backup_s3_bucket_value' => get_option('wps3backup_s3_bucket', ''),
        'wps3backup_s3_api_key_value' => get_option('wps3backup_s3_api_key', ''),
        'wps3backup_s3_api_secret_value' => get_option('wps3backup_s3_api_secret', ''),
        'settings_updated' => isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'],
    );

    include 'includes/settings.tpl.php';
}

