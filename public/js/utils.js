/**
 * Utils for project
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

var paymentAction = function (){

}

function request(form, callback) {
    var formId = $(form).attr('id');
    var $alertDiv = $('#' + formId + '-alert');
    $alertDiv.text('').hide();

    if (form) {
        $.ajax({
            url: '/api/ajax.php',
            type: 'post',
            data: $(form).serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == 'success') {
                    // success
                    callback(form, data);
                } else if (data.status == 'error'){
                    $alertDiv.text(data.message).show();
                    callback(form, data);
                } else {
                    // unknown response
                }
            }
        });
    }
}

$(function () {
    var hash = window.location.hash;
    hash && $('ul.nav a[href="' + hash + '"]').tab('show');
    //TODO: need check somebody hash
    if (!hash || $('ul.nav a[href="' + hash + '"]').length == 0) {
        //TODO: refactoring one function
        var $login = $('ul.nav a[href="#login"]');
        if ($login.length > 0) {
            $login.tab('show');
        }

        var $creteOrder = $('ul.nav a[href="#createOrder"]');
        if ($creteOrder.length > 0) {
            $creteOrder.tab('show');
        }
    }

    // navigation tab activate
    $('#auth-panel .navigation a, #profile .navigation a').click(function (e) {
        $('.error').hide();
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

        request(this, function(form, result) {
            var formId = $(form).attr('id');
            switch (formId){
                case 'form-signin':
                case 'form-join':
                case 'form-user-signout':
                    if (result.status == 'success') {
                        location.reload();
                    }
                    break;
                case 'form-payment':
                    paymentAction();
                    break;
            }
        });

        return false;
    });

});