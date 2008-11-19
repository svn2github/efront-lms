<?php
/**
* Efront Paypal Module
*
* @version 1.0
* @author Tsirakis Nikos
* @date 2008/01/18
*/

/**
 *  Paypal IPN Integration Class
 */
class paypal_class {

    var $last_error;                 // holds the last error encountered
    var $ipn_log;                    // bool: log IPN results to text file?
    var $ipn_log_file;               // filename of the IPN log
    var $ipn_response;               // holds the IPN response from paypal
    var $ipn_data = array();         // array contains the POST values for IPN
    var $fields = array();           // array holds the fields to submit to paypal

    /**
     *  Initialization constructor.  Called when class is created.
     */
    function paypal_class() {
        $this -> paypal_url     = 'https://www.paypal.com/cgi-bin/webscr';
        $this -> last_error     = '';
        $this -> ipn_log_file       = 'ipn_results.log';
        $this -> ipn_log            = true;
        $this -> ipn_response       = '';
    }

    /**
     *  Get paypal module configuration if exists.
     */
    function configuration(){
        $config = eF_getTableData("paypal_configuration", "*", "1=1");
        if(strlen($config['0'][paypalbusiness]) < 5){
            return false;
        }else{
            $conf = array();
            $conf['mailstudents'] = $config['0'][mailstudents];
            $conf['mailprofessors'] = $config['0'][mailprofessors];
            $conf['mailadmins'] = $config['0'][mailadmins];
            return $conf;
        }
    }


    function email_to_admins($subject, $data,$error=false) {
        //eF_mail($from[0]['email'],$_POST['user_email'][$key],$subject,$body);
        $pconfig = $this->configuration();
        if($error){
            $message = "Transaction failed.\n\n$subject\n\n";
            foreach ($data as $key => $value) {
                $message .= "$key => $value\n";
            }
            eF_mail('',$_POST['user_email'][$key],$subject,$body);
        }elseif($pconfig['mailadmins'] == '1'){
            eF_mail('',$_POST['user_email'][$key],$subject,$body);
        }
    }

    function email_to_professors($subject, $data) {
        $pconfig = $this->configuration();
        if($pconfig['mailprofessors'] == '1'){
            eF_mail('',$_POST['user_email'][$key],$subject,$body);
        }
    }

    function email_to_students($subject, $data) {
        $pconfig = $this->configuration();
        if($pconfig['mailstudents'] == '1'){
            eF_mail('',$_POST['user_email'][$key],$subject,$body);
        }
    }

    /**
     *  Function to validate data from paypal
     */
    function validate_ipn() {
        $url_parsed = parse_url($this -> paypal_url);       // parse the paypal URL
        $post_string = "cmd=_notify-validate";              // append ipn command

        foreach ($_POST as $key => $value) {
            $this -> ipn_data["$key"] = $value;
            $value = urlencode(stripslashes($value));
            $post_string .= "&$key = $value";
        }

        //post back to Paypal system to validate
        $header .= "POST $url_parsed[path] HTTP/1.0\r\n";
        $header .= "Host: $url_parsed[host]\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($post_string) . "\r\n\r\n";
        $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30);               // open the connection to paypal
        if(!$fp) {
            $this -> last_error = "fsockopen error no. $errnum: $errstr";
            $this -> log_ipn_results(false);
            return false;
        } else {
            fputs ($fp, $header . $post_string);
            while(!feof($fp)) {
                $this -> ipn_response .= fgets($fp, 1024);
            }
            fclose($fp);
        }
        // Valid IPN
        if (eregi("VERIFIED",$this -> ipn_response)) {
            if(($_SESSION['cart']['business'] == $this -> ipn_data['business']) && ($_SESSION['cart']['item_number'] == $this -> ipn_data['item_number']) && ($_SESSION['cart']['amount'] == $this -> ipn_data['mc_gross'])){
                $this -> insert_transaction_data('VERIFIED');
            }else{
                $this -> insert_transaction_data('NOTVERIFIED');
            }
            $this -> log_ipn_results(true);
            return true;
        // Invalid IPN
        } else {
            $this -> insert_transaction_data('INVALIDIPN');
            $this -> last_error = 'IPN Validation Failed.';
            $this -> log_ipn_results(false);
            return false;
        }
    }

    /**
     *  Function to log results from ipn
     */
    function log_ipn_results($success) {
        if (!$this -> ipn_log) return;
        $text = '['.date('m/d/Y g:i A').'] - ';
        if ($success) $text .= "SUCCESS!\n";
        else $text .= 'FAIL: '.$this -> last_error."\n";
        $text .= "IPN POST Vars from Paypal:\n";
        foreach ($this -> ipn_data as $key=>$value) {
            $text .= "$key=$value, ";
        }
        $text .= "\nIPN Response from Paypal Server:\n ".$this -> ipn_response;
        $fp=fopen($this -> ipn_log_file,'a');
        fwrite($fp, $text . "\n\n");
        fclose($fp);
    }

    /**
     *  Insert data from paypal transaction
     */
    function insert_transaction_data($status){
        $q = eF_getTableData("paypal_configuration", "txnid", "txnid='".$this -> ipn_data['txn_id']."'");
        if(sizeof($q) == 0){
            $input = array(
                            "mc_gross"              => $this -> ipn_data['mc_gross'],
                            "address_status"        => $this -> ipn_data['address_status'],
                            "payer_id"              => $this -> ipn_data['payer_id'],
                            "tax"                   => $this -> ipn_data['tax'],
                            "address_street"        => $this -> ipn_data['address_street'],
                            "payment_date"          => $this -> ipn_data['payment_date'],
                            "payment_status"        => $this -> ipn_data['payment_status'],
                            "charset"               => $this -> ipn_data['charset'],
                            "address_zip"           => $this -> ipn_data['address_zip'],
                            "first_name"            => $this -> ipn_data['first_name'],
                            "mc_fee"                => $this -> ipn_data['mc_fee'],
                            "address_country_code"  => $this -> ipn_data['address_country_code'],
                            "address_name"          => $this -> ipn_data['address_name'],
                            "notify_version"        => $this -> ipn_data['notify_version'],
                            "custom"                => $this -> ipn_data['custom'],
                            "payer_status"          => $this -> ipn_data['payer_status'],
                            "business"              => $this -> ipn_data['business'],
                            "address_country"       => $this -> ipn_data['address_country'],
                            "address_city"          => $this -> ipn_data['address_city'],
                            "quantity"              => $this -> ipn_data['quantity'],
                            "verify_sign"           => $this -> ipn_data['verify_sign'],
                            "payer_email"           => $this -> ipn_data['payer_email'],
                            "txn_id"                => $this -> ipn_data['payer_email'],
                            "payment_type"          => $this -> ipn_data['payer_email'],
                            "last_name"             => $this -> ipn_data['payer_email'],
                            "address_state"         => $this -> ipn_data['payer_email'],
                            "receiver_email"        => $this -> ipn_data['payer_email'],
                            "payment_fee"           => $this -> ipn_data['payer_email'],
                            "receiver_id"           => $this -> ipn_data['payer_email'],
                            "txn_type"              => $this -> ipn_data['payer_email'],
                            "item_name"             => $this -> ipn_data['payer_email'],
                            "mc_currency"           => $this -> ipn_data['payer_email'],
                            "item_number"           => $this -> ipn_data['payer_email'],
                            "residence_country"     => $this -> ipn_data['payer_email'],
                            "test_ipn"              => $this -> ipn_data['payer_email'],
                            "payment_gross"         => $this -> ipn_data['payment_gross'],
                            "shipping"              => $this -> ipn_data['shipping'],
                            "status"                => $status
                            );
            eF_insertTableData('paypal_data', $input);
        }
    }
}
?>