<?php
/**
 * Application utils
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

/**
 * Show json error message
 * @param string $message error message
 */
function error($message){
    echo json_encode(array(
        'status' => 'error',
        'message' => $message
    ));
}

/**
 * Show json message
 * @param array $response array for json message
 * @param bool $isShow true if need show message, if false - return message
 */
function message($response, $isShow = true){
    if (!empty($response)){
        echo json_encode($response);
    } else {
        error('Invalid response data', $isShow);
    }
}

/**
 * Validate required fields
 * @param array $fieldList list required field
 * @return array|bool array with error message or true if all field checked success
 */
function requiredFields($fieldList){
    $validateErrors = array();
    foreach ($fieldList as $field){
        if (isset($_POST[$field]) && !empty($_POST[$field])){
            continue;
        } else {
            $validateErrors[] = "The {$field} is required";
        }
    }
    return (empty($validateErrors)) ? true : $validateErrors;
}

function prepareData($fieldList){
    $result = array();
    foreach ($fieldList as $field) {
        $result[$field] = htmlspecialchars(trim($_POST[$field])); // only xss injection
    }
    return $result;
}

function prepareSQLData($fieldList){
    global $db;
    $result = array();
    foreach ($fieldList as $name=>$value) {
        $result[$name] = mysqli_real_escape_string($db, $value); // sql injection
    }
    return $result;
}

function validateEmailAddress($value){
    if (filter_var($value, FILTER_VALIDATE_EMAIL))
        return true;
    else
        return false;
}