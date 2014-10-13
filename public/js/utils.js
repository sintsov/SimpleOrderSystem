/**
 * Utils for project
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

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
        e.preventDefault()
        $(this).tab('show')
    });

});