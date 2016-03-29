jQuery('#test-settings').click(function() {
    jQuery.post(
        wps3backup_test_settings_options.ajax_url,
        {
            _ajax_nonce: wps3backup_test_settings_options.nonce,
            action: 'wps3backup_test_settings',
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
