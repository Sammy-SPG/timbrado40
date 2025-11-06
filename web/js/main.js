jQuery(document).ready(function ($) {
    $('#formCFDI').on('beforeSubmit', function (e) {
        var $button = $(this).find('button[type=submit]');

        $button.prop('disabled', true);
        $button.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Timbrando, por favor espere...'
        );
        
        return true;
    });
});