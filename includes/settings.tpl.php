<?php
/**
 * @var array $tpl_vars
 * @see wps3backup_settings_page()
 */
?>

<div id="wps3backup-settings" class="wrap">
    <?php
        $backup_dir_errors = get_settings_errors('wps3backup_backup_dir');
        $backup_type_errors = get_settings_errors('wps3backup_backup_type');
        $backup_settings_updated = $tpl_vars['settings_updated'];

        $show_notify = false;

        if (!empty($backup_dir_errors)) {
            $show_notify = true;
            $class = 'notice notice-error';
            $message = array_pop($backup_dir_errors)['message'];
        } elseif (!empty($backup_type_errors)) {
            $show_notify = true;
            $class = 'notice notice-error';
            $message = array_pop($backup_type_errors)['message'];
        } elseif ($backup_settings_updated) {
            $show_notify = true;
            $class = 'notice notice-success';
            $message = translate('Wordpress S3 Backup Settings successfully saved!', 'wps3backup');
        }
    ?>

    <?php if ($show_notify) : ?>
        <div class="<?php echo $class; ?>">
            <p>
                <strong>
                    <?php echo $message ?>
                </strong>
            </p>
        </div>
    <?php endif; ?>

    <h2>Wordpress S3 Backup Settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'wordpress_s3_backup' ); ?>
        <table class="form-table">
            <tr>
                <th colspan="2"><h3>Backup Settings</h3></th>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Backup directory', 'wps3backup' ); ?></th>
                <td>
                    <input type="text" id="wps3backup-backup-dir" name="wps3backup_backup_dir" value="<?= $tpl_vars['wps3backup_backup_dir_value'] ?>">
                    <p class="description">
                        <?php _e( 'We need writable (tmp) directory where we can create backup archives, please use absolute path without trailing slash (ex: "/tmp/s3-backup")', 'wps3backup' ); ?>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Backup type', 'wps3backup' ); ?></th>
                <td>
                    <?php $type_options = $tpl_vars['wps3backup_backup_type_value']; ?>
                    <input
                        type="checkbox"
                        id="wps3backup-backup-type-files"
                        name="wps3backup_backup_type[files]"
                        <?php if (isset($type_options['files']) && $type_options['files']) : ?>
                        checked="checked"
                        <?php endif; ?>
                    >
                    <label for="wps3backup-backup-type-files">Files</label>
                    <br/><br/>
                    <input
                        type="checkbox"
                        id="wps3backup-backup-type-database"
                        name="wps3backup_backup_type[database]"
                        <?php if (isset($type_options['database']) && $type_options['database']) : ?>
                            checked="checked"
                        <?php endif; ?>
                    >
                    <label for="wps3backup-backup-type-database">Database</label>
                    <p class="description">
                        <?php _e( 'Do you want to backup only your files, or your database, or both? Please, choose at least one option', 'wps3backup' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th colspan="2"><h3>S3 Settings</h3></th>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'S3 region', 'wps3backup' ); ?></th>
                <td>
                    <input type="text" id="wps3backup-s3-region" name="wps3backup_s3_region" value="<?= $tpl_vars['wps3backup_s3_region_value'] ?>">
                    <p class="description">
                        <?php _e( 'You can find more information about regions at', 'wps3backup' ); ?>
                        <a href="http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region">
                            http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region
                        </a>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'S3 bucket', 'wps3backup' ); ?></th>
                <td>
                    <input type="text" id="wps3backup-s3-bucket" name="wps3backup_s3_bucket" value="<?= $tpl_vars['wps3backup_s3_bucket_value'] ?>">
                    <p class="description">
                        <?php _e( 'You need to create and configure S3 bucket. More info: ', 'wps3backup' ); ?>
                        <a href="http://docs.aws.amazon.com/AmazonS3/latest/dev/UsingBucket.html">
                            http://docs.aws.amazon.com/AmazonS3/latest/dev/UsingBucket.html
                        </a>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'AWS API Key', 'wps3backup' ); ?></th>
                <td>
                    <input type="text" id="wps3backup-s3-api-key" name="wps3backup_s3_api_key" value="<?= $tpl_vars['wps3backup_s3_api_key_value'] ?>">
                    <p class="description">
                        <?php _e( 'You need to provide Access Key ID. More info:  ', 'wps3backup' ); ?>
                        <a href="http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html">
                            http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html
                        </a>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'AWS API Secret', 'wps3backup' ); ?></th>
                <td>
                    <input type="text" id="wps3backup-s3-api-secret" name="wps3backup_s3_api_secret" value="<?= $tpl_vars['wps3backup_s3_api_secret_value'] ?>">
                    <p class="description">
                        <?php _e( 'You need to provide Secret Access Key. More info:  ', 'wps3backup' ); ?>
                        <a href="http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html">
                            http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html
                        </a>
                    </p>
                </td>
            </tr>

            <tr>
                <th colspan="2"><h3>Test your settings</h3></th>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e( 'Make backup', 'wps3backup' ); ?></th>
                <td>
                    <input type="button" name="backup" id="backup" class="button button-controls" value="Backup my site!">
                    <p class="description">
                        <?php _e( 'We will make a copy of your site according to your backup settings', 'wps3backup' ); ?>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Upload to S3', 'wps3backup' ); ?></th>
                <td>
                    <input type="button" name="upload" id="upload" class="button button-controls" value="Upload my site to S3!">
                    <p class="description">
                        <?php _e( 'We upload latest site archive to Amazon Simple Storage Service according to your S3 settings', 'wps3backup' ); ?>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Save settings', 'wps3backup' ); ?></th>
                <td>
                    <?php submit_button(); ?>
                </td>
            </tr>
        </table>
    </form>
</div>
