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

var orderList = function (lastId){
    $.ajax({
        url: '/api/ajax.php',
        type: 'post',
        data: { action: "/order/list/", lastId: lastId},
        dataType: 'json',
        success: function (response) {
            if (response.status == 'success') {
                if (lastId === 0) {
                    $('#order-list').html(response.data.html);
                } else {
                    $('#loading').remove();
                    $('#order-list').append(response.data.html);
                }
            } else {
                // error & unknown response
                swal('Oops...', "Something went wrong! Try later", "error");
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
            if (response.status == 'success'){
                var data = response.data;
                if (parseInt(data.amount) > 0) {
                    $('#amount').text(data.amount);
                    swal('Congratulation!', response.message, "success");
                    var id = $(form).find('input[name=order-id]').val();
                    var $orderItem = $('div[data-id='+id+']');
                    var $photo = $orderItem.next()

                    $orderItem.hide().fadeOut('slow');
                    $photo.hide().next().hide();
                }
            } else if (response.status == 'error'){
                // error & unknown response
                swal('Oops...', "Something went wrong! Try later", "error");
            }
        }
    });
}

$(function () {

    orderList(0);

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
                            if (parseInt(data.amount) > 0) {
                                $('#amount').text(data.amount);
                                swal('Congratulation!', result.message, "success");
                                $('ul.nav a[href="#createOrder"]').click();
                                clearForm(form);
                                $('.alert-disclemer').remove();
                                $('#create').removeAttr('disabled');
                            }
                        }
                        break;
                    case 'form-createOrder':
                        if (result.status == 'success') {
                            var data = result.data;
                            clearForm(form);
                            swal({
                                title: 'Congratulation!',
                                text: result.message,
                                type: "success"
                            }, function(){
                                if (data.html){
                                    $('#order-list')
                                        .prepend(data.html)
                                        .fadeIn('slow')
                                        .animate({ scrollTop: 0 }, "slow");
                                }
                            });
                        }
                        break;
                }
            });
        }

        return false;
    });

    $('#main').on('scroll', function () {
        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            var $lastElement = $('.order:last');
            var lastId = $lastElement.data('id');

            if (lastId && !$lastElement.data('lastorder')) {
                if ($('#loading').length == 0) {
                    var src = 'data:image/gif;base64,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';
                    $lastElement.append('<img style="padding-left:44px;" id="loading" src="' + src + '" />');
                }
                orderList(lastId);
            }
        }
    });

});