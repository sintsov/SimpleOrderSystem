<?php
/**
 * Init options and core
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

session_start();

$documentRoot = realpath(__DIR__ . '/../../');
require_once 'config.php';
require_once $documentRoot . '/app/libs/users.php';
require_once $documentRoot . '/app/libs/utils.php';

if (!empty($config)) {
    if (isset($config['datebases']) && is_array($config['datebases'])) {
        //TODO: may be lazy loading?
        foreach ($config['datebases'] as $dbName=>$dbItem) {
            $$dbName = mysqli_connect($dbItem['host'], $dbItem['username'], $dbItem['password'], $dbItem['dbname']);
            if (mysqli_connect_errno($$dbName)) {
                die('Failed to connect to MySQL: ' . mysqli_connect_error());
            }
            break; //TODO: need use some datebases
        }
    } else {
        die('Configuration datebases faild!');
    }
} else {
    die('Configuration faild!');
}

?>