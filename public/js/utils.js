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
                $('#order-list').html(response.data.html);

                /*var width = $('#order-list').width();
                $('#scroll').css('width', width).css('height', $('#order-list').height());
                $('#order-list').css('width', width+18).css('height', $('#order-list').height());
                $('#order-list').bind('scroll', function() {
                    console.log(1);
                    if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight){
                        alert('end reached');
                    }
                })*/

                /*$(document).on('mousewheel', function (event) {
                    var $this = $(this);
                    if(event.originalEvent.wheelDelta / 120 < 0) {
                        console.log($('#order-list').height() + ' ' + $this.offset() + ' ' + event.pageY );
                    }
                    //var bottom = $this.offset().top + $this.outerHeight();

                    //if(e.pageY >= bottom) alert("BOTTOM");
                });*/
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
                }
            } else if (response.status == 'error'){
                // error & unknown response
                swal('Oops...', "Something went wrong! Try later", "error");
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
                            if (parseInt(data.amount) > 0) {
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
                            clearForm(form);
                            swal({
                                title: 'Congratulation!',
                                text: result.message,
                                type: "success"
                            }, function(){
                                if (data.html){
                                    $('#order-list').prepend(data.html).fadeIn('slow');
                                }
                            });
                        }
                        break;
                }
            });
        }

        return false;
    });

});