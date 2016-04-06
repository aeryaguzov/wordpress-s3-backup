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
add_action('admin_enqueue_scripts', 'wps3backup_enqueue_admin_scripts');
add_action('wp_ajax_wps3backup_test_settings', 'wps3backup_test_settings_handler');

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
    register_setting('wordpress_s3_backup', 'wps3backup_backup_dir');
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
        'wps3backup_backup_dir_value' => get_option('wps3backup_backup_dir', ''),
        'wps3backup_s3_region_value' => get_option('wps3backup_s3_region', ''),
        'wps3backup_s3_bucket_value' => get_option('wps3backup_s3_bucket', ''),
        'wps3backup_s3_api_key_value' => get_option('wps3backup_s3_api_key', ''),
        'wps3backup_s3_api_secret_value' => get_option('wps3backup_s3_api_secret', ''),
        'settings_updated' => isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'],
    );

    include 'includes/settings.tpl.php';
}

/**
 * Enqueue settings page script (handles "test-settings" button click)
 *
 * @param string $hook
 */
function wps3backup_enqueue_admin_scripts($hook) {
    if ($hook != 'tools_page_s3-backup-settings') {
        return;
    }

    wp_enqueue_script('wps3backup_test_settings', plugin_dir_url(__FILE__) . 'includes/test-settings.js', array('jquery'), 'dev-master', true);

    wp_localize_script(
        'wps3backup_test_settings',
        'wps3backup_test_settings_options',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wps3backup_test_settings'),

        ]
    );
}

/**
 * Test user settings:
 * Upload test file to S3 bucket
 */
function wps3backup_test_settings_handler() {
    check_ajax_referer('wps3backup_test_settings');

    if (empty($_POST['aws_region'])) {
        wp_send_json_error('Empty AWS region');
    } elseif (empty($_POST['s3_bucket'])) {
        wp_send_json_error('Empty S3 bucket');
    } elseif (empty($_POST['aws_key'])) {
        wp_send_json_error('Empty AWS key');
    } elseif (empty($_POST['aws_secret'])) {
        wp_send_json_error('Empty AWS secret');
    }

    require 'vendor/autoload.php';

    $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => $_POST['aws_region'],
        'credentials' => [
            'key' => $_POST['aws_key'],
            'secret' => $_POST['aws_secret']
        ]
    ]);

    $result = [];
    $bucket = $_POST['s3_bucket'];
    $key = 'wps3backup-test';
    $value = 'wps3backup-test-value';

    try {
        $s3->putObject(['Bucket' => $bucket, 'Key' => $key, 'Body' => $value]);

        $result = $s3->getObject(['Bucket' => $bucket, 'Key' => $key]);
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }

    if ($result && !empty($result['Body']) && $result['Body'] == $value) {
        wp_send_json_success(sprintf('Successfully uploaded object with key: "%s"', $key));
    } else {
        wp_send_json_error(sprintf('Failed to upload object with key: "%s"', $key));
    }
}
