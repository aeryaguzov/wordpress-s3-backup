<?php
/**
 * @var array $tpl_vars
 * @see wps3backup_settings_page()
 */
?>

<div class="wrap">
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
            <tr valign="top">
                <th scope="row"><?php _e( 'S3 region', 'wps3backup' ); ?></th>
                <td>
                    <input type="text" name="wps3backup_s3_region" value="<?= $tpl_vars['wps3backup_s3_region_value'] ?>">
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
                    <input type="text" name="wps3backup_s3_bucket" value="<?= $tpl_vars['wps3backup_s3_bucket_value'] ?>">
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
                    <input type="text" name="wps3backup_s3_api_key" value="<?= $tpl_vars['wps3backup_s3_api_key_value'] ?>">
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
                    <input type="text" name="wps3backup_s3_api_secret" value="<?= $tpl_vars['wps3backup_s3_api_secret_value'] ?>">
                    <p class="description">
                        <?php _e( 'You need to provide Secret Access Key. More info:  ', 'wps3backup' ); ?>
                        <a href="http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html">
                            http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html
                        </a>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <td>
                    <?php submit_button(); ?>
                </td>
                <td>
                    <input type="button" name="test-settings" id="test-settings" class="button button-controls" value="Test Settings">
                    <p class="description">
                        <?php _e( 'We upload test file "test-wordpress-s3-backup-settings" to your S3 bucket to ensure you have provided valid settings', 'wps3backup' ); ?>
                    </p>
                </td>
            </tr>
        </table>
    </form>
</div>
