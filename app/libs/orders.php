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
    $userId = getUserInfo('user_id');
    if (isAuth() && $userId) {
        $allowfieldList = array('title', 'cost', 'text');
        if (requiredFields($allowfieldList) === true) {
            $fieldList = prepareData($allowfieldList);
            if (validateMoney($fieldList['cost'])) {
                $fields = prepareSQLData($fieldList);

                $query = sprintf("INSERT INTO orders (status, customer, cost, title, text, created_at)
                                          VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                    1,
                    $userId,
                    $fields['cost'],
                    $fields['title'],
                    $fields['text'],
                    time()
                );
                $result = mysqli_query($db, $query);
                if ($result === true) {
                    $order[] = getOrder(mysqli_insert_id($db));
                    showOrders($order, 'Order successfully create');
                } else {
                    error('Datebase query error: ' . mysqli_error($db));
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
 * Make It
 */
function makeOrder(){
    global $db;
    global $config;

    $userId = getUserInfo('user_id');
    if (isAuth() && $userId) {
        $allowfieldList = array('order-id');
        if (requiredFields($allowfieldList) === true) {
            $fields = prepareSQLData(prepareData($allowfieldList));
            $order = getOrder($fields['order-id']);

            if (!$order){
                error('Order not found');
                die;
            }

            // transaction
            // set autocommit to off
            mysqli_autocommit($db, false);

            // update order
            $query = "UPDATE orders SET employess='" . $userId . "', status='2', modified_at='" . time() . "'
                            WHERE id='" . $fields['order-id'] . "'";

            sqlQuery($db, $query);

            // TODO: transaction log (double entry)
            // ....

            $transactionID = 1;

            if (!isset($config['application']['commission'])){
                error('Not set system comission');
                die;
            }
            $comission = $order['cost']*$config['application']['commission'];
            $cost = $order['cost'] - $comission;

            // update balance customer
            $query = "UPDATE accounts AS a JOIN (SELECT * FROM accounts WHERE user_id = '" . $order['customer'] . "') AS b
                            SET a.balance = b.balance-". $order['cost'] ." WHERE a.user_id = '" . $order['customer'] . "'";
            sqlQuery($db, $query);

            $balance = getUserInfo('balance');
            $totalBalance = $balance + $cost;

            // if don't have account need created
            if (is_null(getUserInfo('account_id'))){
               $accountId = createNewAccount($userId, $totalBalance);
               if ($accountId === false){
                   error('Datebase query error: ' . mysqli_error($db));
                   die;
               }
            } else {
                $accountId = getUserInfo('account_id');
                // update balance employee
                $query = "UPDATE accounts SET balance = '". $totalBalance ."' WHERE id = '" . $accountId . "'";
                sqlQuery($db, $query);
            }

            // system comission
            $query = "INSERT INTO system_account (amount, transaction_id, comission)
                        VALUES ('" . $comission . "', '" . $transactionID . "', '" . $config['application']['commission'] . "')";
            sqlQuery($db, $query);

            // commit
            if (mysqli_commit($db)) {
                // save in session user balance and account id
                // TODO: i think make one function to update props user
                setUserInfo('account_id', $accountId);
                setUserInfo('balance', $totalBalance);

                message(array(
                    'status' => 'success',
                    'message' => 'Order successfully made',
                    'data' => array('amount' => getPrice($totalBalance))
                ));

            } else {
                error('Datebase query error: ' . mysqli_error($db));
            }
        } else {
            error('All fields is required');
        }
    } else {
        error('Problems with user identification');
    }
}

/**
 * List of the order
 */
function orderList($lastId = 0, $limit = 100){
    $orders = getOrders($lastId, $limit);

    if (count($orders) == 0) {
        error('A single order is not created yet');
        die;
    }

    showOrders($orders, 'A list of orders received');
}

function showOrders($orders, $message){
    global $documentRoot;

    ob_start();

    require_once $documentRoot . '/public/layout/orders.php';

    $html = ob_get_clean();
    ob_end_clean();

    message(array(
        'status' => 'success',
        'message' => $message,
        'data' => array(
            'html' => $html,
            'count' => count($orders)
        )
    ));
}

function getOrders($lastId = 0, $limit = 100){
    global $db;
    $orders = array();

    // select only new orders
    if ($lastId > 0) {
        //
    } else {
        $query = "SELECT id, customer, cost, title, text, created_at FROM orders WHERE status='1' ORDER BY id DESC LIMIT " . $limit;
        $result = mysqli_query($db, $query);

        if (!$result) {
            error('Datebase query error: ' . mysqli_error($db));
            die;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }

    return $orders;
}

function getOrder($orderId){
    global $db;

    $query = sprintf("SELECT * FROM orders WHERE id='%s'", mysqli_real_escape_string($db, $orderId));
    $result = mysqli_query($db, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row;
    }

    return false;
}