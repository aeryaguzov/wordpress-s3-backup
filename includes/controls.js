/**
 * Buttons handlers
 */

jQuery('#backup').click(function() {
    jQuery.post(
        wps3backup_controls_options.ajax_url,
        {
            _ajax_nonce: wps3backup_controls_options.nonce,
            action: 'wps3backup_backup',
            backup_dir: jQuery('#wps3backup-backup-dir').val(),
            backup_files: jQuery('#wps3backup-backup-type-files').is(':checked') ? 1 : 0,
            backup_database: jQuery('#wps3backup-backup-type-database').is(':checked') ? 1 : 0
        },
        function (data) {
            var noticeClass = data.success ? 'notice-success' : 'notice-error';

            jQuery('#wps3backup-settings').prepend(
                '<div class="notice ' + noticeClass + ' fade"><p><strong>' + data.data + '</strong></p>'
            );
        }
    );
});


jQuery('#upload').click(function() {
    jQuery.post(
        wps3backup_controls_options.ajax_url,
        {
            _ajax_nonce: wps3backup_controls_options.nonce,
            action: 'wps3backup_upload',
            backup_dir: jQuery('#wps3backup-backup-dir').val(),
            aws_region: jQuery('#wps3backup-s3-region').val(),
            s3_bucket: jQuery('#wps3backup-s3-bucket').val(),
            aws_key: jQuery('#wps3backup-s3-api-key').val(),
            aws_secret: jQuery('#wps3backup-s3-api-secret').val()
        },
        function (data) {
            var noticeClass = data.success ? 'updated' : 'error';
            
            jQuery('#wps3backup-settings').prepend(
                '<div class="' + noticeClass + ' fade"><p><strong>' + data.data + '</strong></p>'
            );
        }
    );
});
