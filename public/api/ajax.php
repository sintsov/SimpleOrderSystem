<?php
/**
 * Ajax endpoint (for request|response)
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once realpath(__DIR__ . '/../../app/config/init.php');

if (!empty($_POST['action'])) {
    switch ($_POST['action']) {
        case '/user/join/':
            userJoin();
            break;
        case '/user/signin/':
            userSignin();
            break;
        case '/user/signout/':
            logout();
            break;
    }
} else {
    error('Please provide url');
}