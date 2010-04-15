<?php
/**

* coupons Class file

*

* @package eFront

* @version 3.6

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * 

 * @author user

 *

 */
class coupons extends EfrontEntity
{
    /**

     * The coupons properties

     * 

     * @since 3.6.0

     * @var array

     * @access public

     */
    public $coupons = array();
    public function __construct($param, $isCouponCode) {
     if ($isCouponCode && eF_checkParameter($param, 'text')) {
      $result = eF_getTableData("coupons", "*", "code='".$param."'");
      $param = $result[0];
     }
     parent :: __construct($param);
    }
    public function checkEligibility($user = false) {
     $returnValue = $this -> checkStartDate() &&
                    $this -> checkActive() &&
                    $this -> checkExpired() &&
                    $this -> checkExceededUses();
  return $returnValue;
    }
    private function checkStartDate() {
     $this -> {$this -> entity}['from_timestamp'] > time() ? $returnValue = false : $returnValue = true;
     return $returnValue;
    }
    private function checkActive() {
     !$this -> {$this -> entity}['active'] ? $returnValue = false : $returnValue = true;
     return $returnValue;
    }
    private function checkExpired() {
     $this -> {$this -> entity}['duration'] && $this -> {$this -> entity}['from_timestamp'] + $this -> {$this -> entity}['duration']*24*3600 < time() ? $returnValue = false : $returnValue = true;
     return $returnValue;
    }
    private function checkExceededUses() {
        $returnValue = true;
  if ($this -> {$this -> entity}['max_uses'] && $this -> getTotalUsedTimes() >= $this -> {$this -> entity}['max_uses']) {
      $returnValue = false;
  }
  if ($this -> {$this -> entity}['max_user_uses'] && ($user instanceOf EfrontUser) && $this -> getUserUsedTimes($user) >= $this -> {$this -> entity}['max_user_uses']) {
      $returnValue = false;
  }
  return $returnValue;
    }
    public function getTotalUsedTimes() {
     $result = eF_getTableData("users_to_coupons", "count(*)", "coupons_ID='".$this -> {$this -> entity}['id']."'");
     return $result[0]['count(*)'];
    }
    public function getUserUsedTimes($user) {
     if (!($user instanceOf EfrontUser)) {
      $user = Efront:: factory($user);
     }
     $result = eF_getTableData("users_to_coupons", "count(*)", "coupons_ID='".$this -> {$this -> entity}['id']."' and users_ID=".$user -> user['id']);
     return $result[0]['count(*)'];
    }
    public function useCoupon($user, $payment, $productsList) {
     if (!($user instanceOf EfrontUser)) {
      $user = Efront:: factory($user);
     }
     if (!($payment instanceOf payments)) {
      $payment = new payments($payment);
     }
     $fields = array('users_ID' => $user -> user['id'],
         'coupons_ID' => $this -> {$this -> entity}['id'],
         'payments_ID' => $payment -> {$payment -> entity}['id'],
         'products_list' => serialize($productsList),
         'timestamp' => time());
     eF_insertTableData("users_to_coupons", $fields);
    }
    public function getCouponStatistics() {
        $result = eF_getTableData("users_to_coupons", "*", "coupons_ID=".$this -> {$this -> entity}['id']);
        $stats = array('total_uses' => sizeof($result),
                       'remaining_uses' => $this -> {$this -> entity}['max_uses'] - sizeof($result) >= 0 ? $this -> {$this -> entity}['max_uses'] - sizeof($result) : 0,
                       'expired' => !$this -> checkExpired(),
                       'valid_until' => $this -> {$this -> entity}['duration'] ? $this -> {$this -> entity}['from_timestamp'] + $this -> {$this -> entity}['duration']*24*3600 : false
        );
        return $stats;
    }
    public function getCouponCourses() {
        $couponCourses = array();
        $courseNames = eF_getTableDataFlat("courses", "id,name");
        $courseNames = array_combine($courseNames['id'], $courseNames['name']);
        $result = eF_getTableData("users_to_coupons", "*", "coupons_ID=".$this -> {$this -> entity}['id']);
        foreach ($result as $value) {
            $products = unserialize($value['products_list']);
            foreach ($products['courses'] as $id) {
                $couponCourses[$value['id']][] = $courseNames[$id];
            }
        }
        return $couponCourses;
    }
    public function getCouponLessons() {
        $couponLessons = array();
        $lessonNames = eF_getTableDataFlat("lessons", "id,name");
        $lessonNames = array_combine($lessonNames['id'], $lessonNames['name']);
        $result = eF_getTableData("users_to_coupons", "*", "coupons_ID=".$this -> {$this -> entity}['id']);
        foreach ($result as $value) {
            $products = unserialize($value['products_list']);
            foreach ($products['lessons'] as $id) {
                $couponLessons[$value['id']][] = $lessonNames[$id];
            }
        }
        return $couponLessons;
    }
    /**

     * Create coupons

     * 

     * This function is used to create coupons

     * <br>Example:

     * <code>

	 * $fields = array("title"       => $form -> exportValue('title'),

	 *       "data"        => $form -> exportValue('data'),

	 *       "timestamp"   => $from_timestamp,

	 *		 "expire"      => $to_timestamp,

	 *       "lessons_ID"  => isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID'] ? $_SESSION['s_lessons_ID'] : 0,

	 *       "users_LOGIN" => $_SESSION['s_login']);

	 *

	 * $coupons = coupons :: create($fields, 0));

	 * 

     * </code>

     * 

     * @param $fields An array of data

     * @param $sendEmail Whether to send the announcement as an email as well 

     * @return coupons The new object

     * @since 3.6.0

     * @access public

     * @static

     */
    public static function create($fields = array(), $sendEmail = false) {
        $fields = array('code' => $fields['code'],
                        'max_uses' => $fields['max_uses'] ? $fields['max_uses'] : 0,
                        'max_user_uses' => $fields['max_user_uses'] ? $fields['max_user_uses'] : 0,
                        'from_timestamp' => $fields['from_timestamp'] ? $fields['from_timestamp'] : time(),
                        'duration' => $fields['duration'] ? $fields['duration'] : 0,
            'discount' => $fields['discount'] ? $fields['discount'] : 0,
                        'active' => $fields['active'] ? $fields['active'] : 1);
        $newId = eF_insertTableData("coupons", $fields);
        $result = eF_getTableData("coupons", "*", "id=".$newId); //We perform an extra step/query for retrieving data, sinve this way we make sure that the array fields will be in correct order (forst id, then name, etc)
        $coupons = new coupons($result[0]['id']);
        return $coupons;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('text', 'code', _COUPONCODE, 'class = "inputText" id = "coupon_code"');
     $form -> addElement('text', 'max_uses', _TOTALUSES, 'class = "inputText" style = "width:50px"');
     $form -> addElement('text', 'max_user_uses', _TOTALUSESBYSINGLEUSER, 'class = "inputText" style = "width:50px"');
     $form -> addElement('text', 'from_timestamp', _VALIDFROM, 'class = "inputText"');
     $form -> addElement('text', 'duration', _DURATION, 'class = "inputText" style = "width:50px"');
     $form -> addElement('text', 'discount', _DISCOUNT, 'class = "inputText" style = "width:50px"');
     $form -> addElement('advcheckbox', 'active', _ACTIVE, null, null, array(0, 1));
     $form -> addElement('submit', 'submit_coupon', _CREATE, 'class = "flatButton"');
     $form -> addRule('code', _THEFIELD.' "'._COUPONCODE.'" '._ISMANDATORY, 'required', null, 'client');
     $form -> addRule('discount', _MAXDISCOUNT100, 'callback', create_function('$a', 'return ($a >= 0 && $a <= 100);')); //The maximum discount is 100
     if ($_GET['edit']) {
      $form -> setDefaults($this -> {$this -> entity});
     } else {
      $form -> setDefaults(array('from_timestamp' => time(),
               'duration' => 30,
               'discount' => 0,
               'active' => 1));
     }
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
     $values = $form -> exportValues();
        $values['from_timestamp'] = mktime(0, 0, 0, $_POST['from_timestamp_Month'], $_POST['from_timestamp_Day'], $_POST['from_timestamp_Year']);
        if (isset($_GET['edit'])) {
            $this -> {$this -> entity}["code"] = $values['code'];
            $this -> {$this -> entity}["max_uses"] = $values['max_uses'];
            $this -> {$this -> entity}["max_user_uses"] = $values['max_user_uses'];
            $this -> {$this -> entity}["from_timestamp"] = $values['from_timestamp'];
            $this -> {$this -> entity}["duration"] = $values['duration'];
            $this -> {$this -> entity}["discount"] = $values['discount'];
            $this -> {$this -> entity}["active"] = $values['active'];
            $this -> persist();
        } else {
         $coupon = self :: create($values);
            $this -> {$this -> entity} = $coupon;
        }
    }
}
?>
