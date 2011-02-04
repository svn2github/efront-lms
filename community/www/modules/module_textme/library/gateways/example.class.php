<?php

/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */

/**
 * 
 *
 */
class Module_TextMe_SmsGateway_Example extends Module_TextMe_SmsGateway {

    public function send($sender, $text, $recipients, $time) {

        $identifiers = array();

        foreach ($recipients as $recipient) {
            $identifiers[] = array(
                'users_LOGIN' => $recipient['users_LOGIN'],
                'identifier' => rand(0, 10000)
            );
        }

        return $identifiers;
    }

    public function query($identifier, $mobile) {

        $status = null;

        switch (rand(0, 10000)%3) {
            case 0:
                $status = 'failed';
                break;
            case 1:
                $status = 'delivered';
                break;
            case 2:
            default:
                $status = 'pending';
                break;
        }

        return $status;
    }

    public function test() {

        return rand(0, 10000);
    }

    public function supportsDeliveryReports() {
        return true;
    }

    public function supportsScheduledMessages() {
        return true;
    }

    public function getMandatoryParameters($to_string = false) {
        if ($to_string) {
            $mandatory = '';
        } else {
            $mandatory = array();
        }
        return $mandatory;
    }

}

?>
