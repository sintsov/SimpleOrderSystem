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
                                    'user_id' => mysqli_insert_id($db),
                                    'name' => $fields['name'],
                                    'email' => $fields['email'],
                                    'role_id' => $fields['role'],
                                    'account_id' => null,
                                    'balance' => 0,
                                    'reserve' => 0
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
                            'user_id' => $row['id'],
                            'name' => $row['name'],
                            'email' => $row['email'],
                            'role_id' => $row['role_id'],
                            'account_id' => $row['account_id'],
                            'balance' => is_null($row['account_id']) ? 0 : getBalanceByAccountId($row['account_id']),
                            'reserve' => is_null($row['account_id']) ? 0 : getReserveByAccountId($row['account_id'])
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

function isCustomer(){
    return (getUserInfo('role_id') == 1) ? true : false;
}

function getBalanceByAccountId($accountId){
    global $db;
    if (is_numeric($accountId)) {
        $query = "SELECT balance FROM accounts WHERE id='" . $accountId . "'";
        $result = mysqli_query($db, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['balance'];
        }
    }
    return false;
}

function getReserveByAccountId($accountId){
    global $db;
    if (is_numeric($accountId)) {
        $query = "SELECT reserve FROM accounts WHERE id='" . $accountId . "'";
        $result = mysqli_query($db, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['reserve'];
        }
    }
    return false;
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
    } else
        return false;
}

function setUserInfo($key, $value){
    if (getUserSession()){
        $_SESSION['auth'][$key] = $value;
        return true;
    } else
        return false;
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

function paymentUser(){
    global $db;
    $userId = getUserInfo('user_id');
    if (isAuth() && $userId){
        $allowfieldList = array('payment_id', 'total');
        if (requiredFields($allowfieldList) === true){
            $fieldList = prepareData($allowfieldList);
            if (validateMoney($fieldList['total'])){
                $fields = prepareSQLData($fieldList);
                // transaction
                // set autocommit to off
                mysqli_autocommit($db, false);

                // if don't have account need created
                if (is_null(getUserInfo('account_id'))){
                    // create new account for user and set balance
                    $accountId = createNewAccount($userId, $fields['total']);
                    if ($accountId === false){
                        error('Datebase query error: ' . mysqli_error($db));
                        die;
                    }

                    // associate a user with his account
                    $query = "UPDATE users SET account_id = '" . $accountId . "', modified_at = '" . time() . "'
                                    WHERE id = '" . $userId . "'";
                    sqlQuery($db, $query);

                    $totalBalance = $fields['total'];

                } else {
                    $accountId = getUserInfo('account_id');
                    $balance = getUserInfo('balance');
                    $totalBalance = $balance + $fields['total'];

                    // update balance user
                    $query = "UPDATE accounts SET balance = '" . $totalBalance . "' WHERE user_id = '" . $userId . "'";
                    sqlQuery($db, $query);

                    $totalBalance = getBalanceByAccountId($accountId);
                }

                // store transaction
                $query = sprintf("INSERT INTO external_transactions (credit, payment_system, amount, date)
                                          VALUES ('%s', '%s', '%s', '%s')",
                    $accountId,
                    $fields['payment_id'],
                    $fields['total'],
                    time()
                );
                sqlQuery($db, $query);

                if (mysqli_commit($db)) {

                    // save in session user balance and account id
                    // TODO: i think make one function to update props user
                    setUserInfo('account_id', $accountId);
                    setUserInfo('balance', $totalBalance);

                    message(array(
                        'status' => 'success',
                        'message' => 'Payment was successful',
                        'data' => array('amount' => getPrice($totalBalance))
                    ));

                } else {
                    error('Transaction commit failed');
                }
            } else {
                error('Enter the amount of payment');
            }
        } else {
            error('All fields is required');
        }
    } else {
        error('Problems with user identification');
    }
}

/**
 * Create new account for user and set balance
 * @param int $userId user identificator
 * @param float|int $balance balance
 * @return bool|int|string account id, or false is create fail
 */
function createNewAccount($userId, $balance){
    global $db;

    $query = sprintf("INSERT INTO accounts (user_id, balance, reserve) VALUES ('%s', '%s', '0')",  $userId, $balance);
    $result = mysqli_query($db, $query);
    if ($result !== true){
        return false;
    } else
        return mysqli_insert_id($db);
}

function getUser($userId){
    global $db;

    $query = sprintf("SELECT * FROM users WHERE id='%s'", mysqli_real_escape_string($db, $userId));
    $result = mysqli_query($db, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row;
    }

    return false;
}