<?php

/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */

/**
 * Description of ez4usms
 *
 * @author manos
 */

class Module_TextMe_SmsGateway_SmsOne extends Module_TextMe_SmsGateway {

    private $wsdl_uri = 'http://sse.smsone.gr/api/soap/sms.wsdl.php';

    public function send($sender, $text, $recipients, $time) {

        $client = new SoapClient($this->wsdl_uri);

        $to = new stdClass();
        $to->recipients = array();

        foreach ($recipients as $recipient) {
            $to->recipients[] = $recipient['mobile'];
        }

        $id = $client->send($this->username, $this->password,
                        $sender, $to, $text, 'UTF-8', false);

        $identifiers = array();

        foreach ($recipients as $recipient) {
            $identifiers[] = array(
                'users_LOGIN' => $recipient['users_LOGIN'],
                'id' => $id
            );
        }

        return $identifiers;
    }

    public function getMandatoryParameters($to_string = false) {
        if ($to_string) {
            $mandatory = 'username, password';
        } else {
            $mandatory = array('username', 'password');
        }

        return $mandatory;
    }

    public function query($identifier, $mobile) {

        $client = new SoapClient($this->wsdl_uri);

        $status = $client->query($this->username, $this->password, $identifier, $mobile);

        return $status;
    }

    public function test() {

        $client = new SoapClient($this->wsdl_uri);


        $to = new stdClass();
        $to->recipients = array($this->mobile);

        $text = 'This is a test message. Routed by www.smsone.gr.';

        $id = $client->send($this->username, $this->password,
                        'eFront', $to, $text, 'UTF-8', false);
        return $id;
    }

    public function supportsDeliveryReports() {
        return true;
    }

    public function supportsScheduledMessages() {
        return false;
    }

}

?>
