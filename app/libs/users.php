<?php
/**
 * The basic functionality for working with the entity of the user
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

/**
 *  Join new user
 */
function userJoin(){
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
 * Sig in user
 */
function userSignin(){
    global $db;
    $allowfieldList = array('email', 'password');
    if (requiredFields($allowfieldList) === true){
        $fieldList = prepareData($allowfieldList);
        $hash = getUserHash($fieldList['email']);
        var_dump($hash);
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

function getUserHash($email){
    global $db;
    $query = sprintf("SELECT password FROM users WHERE email='%s'", mysqli_real_escape_string($db, $email));
    $result = mysqli_query($db, $query);
    while ($row = mysqli_fetch_assoc($result)){
        return $row['password'];
    }
    return false;
}

function setUserSession($data){
    if (is_array($data) && !empty($data)){
        foreach ($data as $name=>$value){
            $_SESSION['auth'][$name] = $value;
        }
    }
}

function getUserSession(){
    return (isset($_SESSION['auth']) && !empty($_SESSION['auth'])) ? $_SESSION['auth'] : false;
}

function getUserInfo($key){
    if (getUserSession()){
        return $_SESSION['auth'][$key];
    }
}

function isAuth(){
    if (getUserSession())
        return true;
    else
        return false;
}

function logout(){
    if (getUserSession()) {
        unset($_SESSION['auth']);
    }
}

/**
 * Check user exists use email
 */
function isUserExists($email){
    global $db;
    $query = sprintf("SELECT * FROM users WHERE email='%s'", mysqli_real_escape_string($db, $email));
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) > 0){
        return true;
    } else
        return false;
}

/**
 * Get security hash password with a random salt (work only PHP > 5.5)
 * @param string $password user password
 * @param int $cost A higher "cost" is more secure but consumes more processing power
 * @return string security hash password
 */
function getHashPassword($password, $cost = 10){
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => $cost, 'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)));
}