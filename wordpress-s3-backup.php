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

defined('WPS3BACKUP_LATEST_BACKUP_NAME') || define('WPS3BACKUP_LATEST_BACKUP_NAME', 'latest.zip');

add_action('admin_menu', 'wps3backup_add_menu');
add_action('admin_init', 'wps3backup_register_settings');
add_action('admin_enqueue_scripts', 'wps3backup_enqueue_admin_scripts');

add_action('wp_ajax_wps3backup_backup', 'wps3backup_backup_handler');
add_action('wp_ajax_wps3backup_upload', 'wps3backup_upload_handler');

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
    // Main settings
    register_setting('wordpress_s3_backup', 'wps3backup_backup_dir', function($input) {
        if (empty($input)) {
            add_settings_error('wps3backup_backup_dir', 100500, translate('Backup directory is empty!', 'wps3backup'));
        }

        return $input;
    });

    register_setting('wordpress_s3_backup', 'wps3backup_backup_type', function($input) {
        if (empty($input)) {
            add_settings_error('wps3backup_backup_type', 100500, translate('Backup options are empty!', 'wps3backup'));
        }

        return $input;
    });

    // S3 settings
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
        'wps3backup_backup_type_value' => get_option('wps3backup_backup_type', ''),
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

    wp_enqueue_script(
        'wps3backup_controls',
        plugin_dir_url(__FILE__) . 'includes/controls.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script(
        'wps3backup_controls',
        'wps3backup_controls_options',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wps3backup_controls'),

        ]
    );
}

/**
 * Test user settings: make backup
 */
function wps3backup_backup_handler() {
    check_ajax_referer('wps3backup_controls');

    if (empty($_POST['backup_dir'])) {
        wp_send_json_error('Empty backup directory');
    }

    $backupDir = realpath($_POST['backup_dir']);

    if (!$backupDir) {
        wp_send_json_error('Backup directory does not exist');
    }

    if (!is_writable($backupDir)) {
        wp_send_json_error('Backup directory is not writable');
    }

    try {
        wps3backup_make_backup($backupDir);
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }

    if (file_exists($backupDir . DIRECTORY_SEPARATOR . WPS3BACKUP_LATEST_BACKUP_NAME)) {
        wp_send_json_success(
            sprintf(
                'Successfully created backup for your site! See "%s" in your backup directory',
                WPS3BACKUP_LATEST_BACKUP_NAME
            )
        );
    } else {
        wp_send_json_error(
            sprintf(
                'Failed to create backup for your site. Can\'t find "%s" in your backup directory',
                WPS3BACKUP_LATEST_BACKUP_NAME
            )
        );
    }
}

/**
 * Test user settings: upload backup to S3
 */
function wps3backup_upload_handler() {
    check_ajax_referer('wps3backup_controls');

    if (empty($_POST['backup_dir'])) {
        wp_send_json_error('Empty backup directory');
    }

    if (!realpath($_POST['backup_dir'])) {
        wp_send_json_error('Backup directory does not exist');
    }

    $backupFile = realpath(
        realpath($_POST['backup_dir'])
        . DIRECTORY_SEPARATOR
        . WPS3BACKUP_LATEST_BACKUP_NAME
    );

    if (!$backupFile) {
        wp_send_json_error(
            sprintf(
                'Can\'t find "%s" in backup directory, please run backup first',
                WPS3BACKUP_LATEST_BACKUP_NAME
            )
        );
    }

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

    $bucket = $_POST['s3_bucket'];
    $key = str_replace(' ', '_', strtolower(get_bloginfo())) . '_' . date('Y_m_d');

    try {
        $s3->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $backupFile
        ]);
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }

    wp_send_json_success(sprintf('Successfully uploaded object with key: "%s"', $key));
}

/**
 * Make site archive and store it in backup directory
 *
 * @param string $backupDir
 */
function wps3backup_make_backup($backupDir) {
    $filename = $backupDir . DIRECTORY_SEPARATOR . WPS3BACKUP_LATEST_BACKUP_NAME;

    if (file_exists($filename)) {
        unlink($filename);
    }

    $archive = new ZipArchive();
    $archive->open($filename, ZipArchive::CREATE);

    $rootPath = wps3backup_get_root_path(__DIR__);

    if (!$rootPath) {
        throw new RuntimeException('Unable to find site root path');
    }

    $archive = wps3backup_fill_archive($rootPath, $archive, $rootPath . DIRECTORY_SEPARATOR);
    $archive->close();
}

/**
 * Find site root path
 *
 * @param string $path
 * @return bool|string
 */
function wps3backup_get_root_path($path) {
    if (defined('ABSPATH')) {
        return realpath(ABSPATH);
    }

    $path = realpath($path);

    if (!$path) {
        return false;
    }

    if (file_exists($path . DIRECTORY_SEPARATOR. 'wp-config.php')) {
        return $path;
    }

    return wps3backup_get_root_path(dirname($path));
}

/**
 * Recursively fill archive with files
 *
 * @param string $path
 * @param ZipArchive $archive
 * @param string $stripPath - path to strip file names
 * @return ZipArchive
 */
function wps3backup_fill_archive($path, ZipArchive $archive, $stripPath) {

    $iterator = new \RecursiveDirectoryIterator(
        $path,
        \FilesystemIterator::SKIP_DOTS
    );

    /** @var \SplFileInfo $item */
    foreach($iterator as $item) {
        if ($item->isDir()) {
            $archive = wps3backup_fill_archive($item->getRealPath(), $archive, $stripPath);
        } elseif ($item->isFile()) {
            $archive->addFile(
                $item->getRealPath(),
                str_replace($stripPath, '', $item->getRealPath())
            );
        }
    }

    return $archive;
}
