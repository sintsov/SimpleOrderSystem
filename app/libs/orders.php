<?php
/**
 * The basic functionality for working with the entity orders
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

/**
 *  Create new order
 */
function createOrder(){
    global $db;
    $allowfieldList = array('name', 'email', 'password', 'confirmPassword', 'role');
    if (requiredFields($allowfieldList) === true){
            $fieldList = prepareData($allowfieldList);
            if (validateEmailAddress($fieldList['email'])){
                if ($fieldList['password'] == $fieldList['confirmPassword']) {
                    if (!isUserExists($fieldList['email'])){
                        $fields = prepareSQLData($fieldList);
                        $query = sprintf("INSERT INTO users (name, email, password, role_id, time_visited, created_at)
                                          VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                            $fields['name'],
                            $fields['email'],
                            getHashPassword($fields['password']),
                            $fields['role'],
                            time(),
                            time()
                        );
                        $result = mysqli_query($db, $query);
                        if ($result === true){
                            message(array(
                                'status' => 'success',
                                'message' => 'User successfully create'
                            ));
                            setUserSession(
                                array(
                                    'name' => $fields['name'],
                                    'email' => $fields['email'],
                                    'role_id' => $fields['role']
                                )
                            );
                        } else {
                            error('Datebase query error: ' . mysqli_error($db));
                        }
                    } else {
                        error('User with this email already exists');
                    }
                } else {
                    error('The passwords do not match');
                }
            } else {
                error('The e-mail is not valid');
            }
    } else if (!isset($_POST['role'])){
        error('Need choose your role: Cutsomer or Employee');
    } else {
        error('All fields is required');
    }
}

/**
 * Make It
 */
function makeOrder(){
    global $db;
    $allowfieldList = array('email', 'password');
    if (requiredFields($allowfieldList) === true){
        $fieldList = prepareData($allowfieldList);
        $hash = getUserHash($fieldList['email']);
        if ($hash && password_verify($fieldList['password'], $hash)) {
            $query = sprintf("SELECT * FROM users WHERE email='%s'", mysqli_real_escape_string($db, $fieldList['email']));
            $result = mysqli_query($db, $query);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    message(array(
                        'status' => 'success',
                        'message' => 'User successfully authorization'
                    ));
                    setUserSession(
                        array(
                            'name' => $row['name'],
                            'email' => $row['email'],
                            'role_id' => $row['role_id']
                        )
                    );
                }
            } else {
                error('Datebase query error: ' . mysqli_error($db));
            }
        } else {
            error('Invalid email or password');
        }
    } else {
        error('All fields is required');
    }
}

/**
 * List of the order
 */
function orderList(){

}