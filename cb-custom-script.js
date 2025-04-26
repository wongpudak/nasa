jQuery(document).ready(function ($) {
    // Di dalam file JavaScript Anda
    var data = {
        'action': 'get_sponsor_data'
    };

    // Menggunakan fungsi jQuery.ajax untuk membuat permintaan ke admin-ajax.php
    $.post(myAjax.ajaxurl, data, function (response) {
        // Mengganti [sp_nama] dengan nama sponsor
        replaceShortcode(response.sponsorName);
    });

    function replaceShortcode(sponsorName) {
        // Mengganti [sp_nama] dengan nama sponsor di seluruh konten
        var bodyContent = document.body.innerHTML;
        document.body.innerHTML = bodyContent.replace(/\[sp_nama\]/g, sponsorName);
    }
});
