<?php
/**

* EfrontPayments Class file

*

* @package eFront

* @version 3.6

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
class EfrontPaymentsException extends Exception
{
    const INADEQUATE_BALANCE = 1201;
    const TRANSACTION_HTTP_ERROR = 1202;
    const BUSINESS_ADDRESS_MISMATCH = 1203;
    const UNSUPPORTED_OPERATION_TYPE = 1204;
    const DUPLICATE_PAYMENT = 1205;
    const UNSUPPORTED_PAYMENT = 1206;
}
/**

 *

 * @author user

 *

 */
class payments extends EfrontEntity
{
    /**

     * Valid payment methods

     * @var array

     * @since 3.6.0

     */
    public static $methods = array('paypal' => _PAYPAL,
              'manual' => _MANUAL,
                                   'balance' => _BALANCE);
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     //$form -> addElement('select', 'user', _USER, 'class = "inputText"');
     //$form -> addRule('user', _THEFIELD.' "'._USER.'" '._ISMANDATORY, 'required', null, 'client');
     $form -> addElement('text', 'amount', _AMOUNT, 'class = "inputTextScore"');
     $form -> addRule('amount', _THEFIELD.' "'._AMOUNT.'" '._MUSTBENUMERIC, 'numeric', null, 'client');
     $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "simpleEditor inputTextarea"');
     $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
     //$form -> setDefaults(array('title' => $this -> news['title'], 'data' => $this -> news['data']));
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        formatLogin();
        $flippedLogins = array_flip($GLOBALS['_usernames']);
        $timestamp = mktime($_POST['payment_Hour'], $_POST['payment_Minute'], 0, $_POST['payment_Month'], $_POST['payment_Day'], $_POST['payment_Year']);
        $values = $form -> exportValues();
        $fields = array("amount" => $values['amount'],
               "comments" => $values['comments'],
                        "timestamp" => $timestamp,
                        "method" => "manual",
                        "users_LOGIN" => $flippedLogins[$_POST['user']]);
        $payments = self :: create($fields);
        $this -> payments = $payments;
    }
    /**

     * Create Payment

     *

     * This function is used to create a new payment entry

     *

     * @param array $fields The payment properties

     * @return payment The created payment

     * @since 3.6.0

     * @access public

     */
    public static function create($fields = array()) {
     $fields['lessons'] = array_filter($fields['lessons'], 'is_numeric');
     if (isset($fields['lessons']) && sizeof($fields['lessons']) > 0) {
      $lessonNames = eF_getTableDataFlat("lessons", "name", "id in (".implode(",", $fields['lessons']).")");
     }
     $fields['courses'] = array_filter($fields['courses'], 'is_numeric');
     if (isset($fields['courses']) && sizeof($fields['courses']) > 0) {
      $courseNames = eF_getTableDataFlat("courses", "name", "id in (".implode(",", $fields['courses']).")");
     }
     !isset($fields['charset']) OR $fields['comments'] = iconv($fields['charset'], "UTF-8", $fields['comments']);
     $fields = array('timestamp' => isset($fields['timestamp']) && eF_checkParameter($fields['timestamp'], 'timestamp') ? $fields['timestamp'] : time(),
                        'users_LOGIN' => isset($fields['users_LOGIN']) && eF_checkParameter($fields['users_LOGIN'], 'login') ? $fields['users_LOGIN'] : $_SESSION['s_login'],
                        'amount' => isset($fields['amount']) && is_numeric($fields['amount']) && $fields['amount'] > 0 ? $fields['amount'] : 0,
                        'status' => isset($fields['status']) && $fields['status'] ? $fields['status'] : 'completed',
                        'txn_id' => $fields['txn_id'],
                        'comments' => $fields['comments'],
                        'method' => isset($fields['method']) && in_array($fields['method'], array_keys(self :: $methods)) ? $fields['method'] : 'manual');
        $user = EfrontUserFactory :: factory($fields['users_LOGIN']);
        if ($fields['method'] == 'paypal') {
            //@todo: get corresponding paypal_data id
   $eventType = EfrontEvent::NEW_PAYPAL_PAYMENT;
        } else if ($fields['method'] == 'balance') {
   $eventType = EfrontEvent::NEW_BALANCE_PAYMENT;
        } else {
         $eventType = false;
        }
        $newId = eF_insertTableData("payments", $fields);
        $result = eF_getTableData("payments", "*", "id=".$newId); //We perform an extra step/query for retrieving data, since this way we make sure that the array fields will be in correct order (first id, then name, etc)
        $payment = new payments($result[0]['id']);
        if ($eventType) {
         $event = array("type" => $eventType,
          "users_LOGIN" => $user -> user['login'],
          "users_name" => $user -> user['name'],
          "users_surname" => $user -> user['surname'],
          "entity_ID" => $newId);
         if (isset($lessonNames) && !empty($lessonNames)) {
          $event['lessons_name'] = _LESSONS.': '.implode(",", $lessonNames['name']).'<br>';
         }
         if (isset($courseNames) && !empty($courseNames)) {
          $event['lessons_name'] .= _COURSES.': '.implode(",", $courseNames['name']);
         }
         if ($fields['credit']) {
          $event['lessons_name'] .= _BALANCE.': '.$fields['credit'];
         }
   EfrontEvent::triggerEvent($event);
        }
        return $payment;
    }
}
/**

 *

 * @author user

 *

 */
class cart
{
    /**

     *

     * @return unknown_type

     */
    public static function retrieveCart() {
        if (isset($_COOKIE['cart']) && is_numeric($_COOKIE['cart'])) {
            $result = eF_getTableData("carts", "contents", "id=".$_COOKIE['cart']);
            $cart = unserialize($result[0]['contents']);
        } else {
            $cart = array();
        }
        return $cart;
    }
    /**

     *

     * @param $cart

     * @return unknown_type

     */
    public static function prepareCart($cart = false) {
        if (!$cart) {
            $cart = self :: retrieveCart();
        }
        $cartTotal = 0;
        if (isset($cart['lesson'])) {
            foreach ($cart['lesson'] as $key => $entry) {
                $lesson = new EfrontLesson($entry);
                //Recurring items cannot coexist with anything else in the cart. For this reason, when a recurring item is added in the cart,
                //everything else is removed. If we reached this point and there are recurring items alongside non-recurring, this means that we
                //first added a recurring item and then a non-recurring. In this case, we must remove any recurring items from the cart.
                if ($lesson -> options['recurring'] && (sizeof($cart['lesson']) > 1 || !empty($cart['course']))) {
                    unset($cart['lesson'][$key]);
                } else {
                 $lesson -> lesson['price_string'] = formatPrice($lesson -> lesson['price'], array($lesson -> options['recurring'], $lesson -> options['recurring_duration']), true);
                 $cart['lesson'][$key] = $lesson -> lesson;
                 $cart['lesson'][$key]['recurring'] = $lesson -> options['recurring'];
                 $cart['lesson'][$key]['recurring_duration'] = $lesson -> options['recurring_duration'];
                 if ($GLOBALS['configuration']['discount_start'] < time() && $GLOBALS['configuration']['discount_start'] + $GLOBALS['configuration']['discount_period']*3600*24 > time()) {
                     $cartTotal += $lesson -> lesson['price'] - $lesson -> lesson['price'] * $GLOBALS['configuration']['total_discount']/100;
                 } else {
                     $cartTotal += $lesson -> lesson['price'];
                 }
                }
            }
        } else {
            $cart['lesson'] = array();
        }
        if (isset($cart['course'])) {
            foreach ($cart['course'] as $key => $entry) {
                $course = new EfrontCourse($entry);
                //Recurring items cannot coexist with anything else in the cart. For this reason, when a recurring item is added in the cart,
                //everything else is removed. If we reached this point and there are recurring items alongside non-recurring, this means that we
                //first added a recurring item and then a non-recurring. In this case, we must remove any recurring items from the cart.
                if ($course -> options['recurring'] && (sizeof($cart['course']) > 1 || !empty($cart['lesson']))) {
                    unset($cart['course'][$key]);
                } else {
                 $course -> course['price_string'] = formatPrice($course -> course['price'], array($course -> options['recurring'], $course -> options['recurring_duration']), true);
                 $cart['course'][$key] = $course -> course;
                 $cart['course'][$key]['recurring'] = $course -> options['recurring'];
                 $cart['course'][$key]['recurring_duration'] = $course -> options['recurring_duration'];
                 if ($GLOBALS['configuration']['discount_start'] < time() && $GLOBALS['configuration']['discount_start'] + $GLOBALS['configuration']['discount_period']*3600*24 > time()) {
                     $cartTotal += $course -> course['price'] - $course -> course['price'] * $GLOBALS['configuration']['total_discount']/100;
                 } else {
                     $cartTotal += $course -> course['price'];
                 }
                }
            }
        } else {
            $cart['course'] = array();
        }
        if (isset($cart['credit'])) {
            $cartTotal += $cart['credit'];
        } else {
            $cart['credit'] = 0;
        }
        if ($cartTotal) {
            $cart['total_price'] = $cartTotal;
            $cart['total_price_string'] = formatPrice($cartTotal, false, false);
        }
        return $cart;
    }
    /**

     *

     * @param $cart

     * @return unknown_type

     */
    public static function storeCart($cart = false) {
        if (!$cart) {
            setcookie("cart", false, 1, "/");
        } else {
         $cart = self :: compactCart($cart);
         if ($cart === false) {
             setcookie("cart", false, 1, "/");
         } else {
             //Check whether a cart for this session id exists and if yes update, otherwise create
             $result = eF_getTableData("carts", "id", "session_id='".session_id()."'");
             if (sizeof($result) > 0) {
                 eF_updateTableData("carts", array("contents" => serialize($cart), "timestamp" => time()), "id=".$result[0]['id']);
                 $id = $result[0]['id'];
             } else {
                 $id = eF_insertTableData("carts", array("contents" => serialize($cart), "timestamp" => time(), "session_id" => session_id()));
             }
             setcookie("cart", $id, time() + 3600*24, "/", false, false, true);
             //Delete carts older than a day
             eF_deleteTableData("carts", "timestamp < ".(time() - 86400));
         }
        }
        return $cart;
    }
    /**

     *

     * @param $cart

     * @return unknown_type

     */
    public static function compactCart($cart) {
        unset($cart['total_price_string']);
        unset($cart['total_price']);
        if (empty($cart['lesson'])) {
            unset($cart['lesson']);
        }
        if (empty($cart['course'])) {
            unset($cart['course']);
        }
        if ($cart['credit'] == 0) {
            unset($cart['credit']);
        }
        if (empty($cart)) {
            unset($cart);
            return false;
        } else {
            return $cart;
        }
    }
    /**

     *

     * @param $cart

     * @param $lessons

     * @param $courses

     * @return unknown_type

     */
    public static function filterCart($cart, $lessons = array(), $courses = array()) {
        if (isset($cart['lesson'])) {
            foreach ($cart['lesson'] as $key => $entry) {
                if (!in_array($key, array_keys($lessons))) {
                    unset($cart['lesson'][$key]);
                }
            }
        }
        if (isset($cart['course'])) {
            foreach ($cart['course'] as $key => $entry) {
                if (!in_array($key, array_keys($courses))) {
                    unset($cart['course'][$key]);
                }
            }
        }
        $cart = self :: compactCart($cart);
        return $cart;
    }
}
