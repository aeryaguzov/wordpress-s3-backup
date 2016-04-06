<?php
/**
 * @var array $tpl_vars
 * @see wps3backup_settings_page()
 */
?>

<div id="wps3backup-settings" class="wrap">
    <?php if ($tpl_vars['settings_updated']) : ?>
        <div class="updated fade">
            <p>
                <strong>
                    <?php _e( 'Wordpress S3 Backup Settings successfully saved!', 'wps3backup' ); ?>
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
                        <?php _e( 'We need writable directory where we can create backup archives, please use absolute path without trailing slash (ex: "/home/wordpress/backup")', 'wps3backup' ); ?>
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
