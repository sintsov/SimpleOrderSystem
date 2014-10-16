/**
 * Utils for project
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

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

var clearForm = function (form){
    $('form').find("input[type=text], textarea").val("");
}

var orderList = function (){
    $.ajax({
        url: '/api/ajax.php',
        type: 'post',
        data: { action: "/order/list/"},
        dataType: 'json',
        success: function (response) {
            if (response.status == 'success') {
                $('#order-list').html(response.data.html)
            } else if (response.status == 'error'){
               // error
            } else {
                // unknown response
            }
        }
    });
}

var makeIt = function (form){
    $.ajax({
        url: '/api/ajax.php',
        type: 'post',
        data: $(form).serialize(),
        dataType: 'json',
        success: function (response) {
            if (result.status == 'success'){
                var data = result.data;
                swal('Congratulation!', result.message, "success");
            } else if (response.status == 'error'){
                // error
            } else {
                // unknown response
            }
        }
    });
}

$(function () {

    orderList();

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
    $(document).on('submit', 'form', function (e) {
        e.preventDefault();

        if ($(this).hasClass('form-order-makeit')){
            makeIt(this);
        } else {
            request(this, function (form, result) {
                var formId = $(form).attr('id');
                switch (formId) {
                    case 'form-signin':
                    case 'form-join':
                    case 'form-user-signout':
                        if (result.status == 'success') {
                            location.reload();
                        }
                        break;
                    case 'form-payment':
                        if (result.status == 'success') {
                            var data = result.data;
                            if (data.amount > 0) {
                                $('#amount').text(data.amount);
                                swal('Congratulation!', result.message, "success");
                                $('ul.nav a[href="#createOrder"]').click();
                                clearForm(form);
                            }
                        }
                        break;
                    case 'form-createOrder':
                        if (result.status == 'success') {
                            var data = result.data;
                            swal('Congratulation!', result.message, "success");
                            clearForm(form);
                        }
                        break;
                }
            });
        }

        return false;
    });

});