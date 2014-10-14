/**
 * Utils for project
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

var request = function (form) {
    var formId = $(form).attr('id');
    var $alertDiv = $('#' + formId + '-alert');
    $alertDiv.text('').hide();

    var isSuccessfully = false;

    if (form) {
        $.ajax({
            url: '/api/ajax.php',
            type: 'post',
            data: $(form).serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == 'success') {
                    // success
                    isSuccessfully = true;
                } else if (data.status == 'error'){
                    $alertDiv.text(data.message).show();
                } else {
                    // unknown response
                }
            }
        });
    }
    return isSuccessfully;
}

$(function () {
    var hash = window.location.hash;
    hash && $('ul.nav a[href="' + hash + '"]').tab('show');
    //TODO: need check somebody hash
    if (!hash) {
        var $login = $('ul.nav a[href="#login"]');
        if ($login.length > 0) {
            $login.tab('show');
        }
    }

    // navigation tab activate
    $('#auth-panel .navigation a').click(function (e) {
        $('.alert').remove();
        $(this).tab('show');
        window.location.hash = this.hash;
    });

    // choose role tab
    $('#join .nav a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // all form submit events listen
    $('form').submit(function (e) {
        e.preventDefault();
        var result = request(this);
        return false;
    });

});