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
class Module_TextMe_SmsGatewayFactory {

    private final function __construct() {
        ;
    }

    private final function __clone() {
        ;
    }

    public static function getGateways() {
        $gateways = array(
            'www.ez4usms.com' => 'www.ez4usms.com',
            'www.smsn.gr' => 'www.smsn.gr',
            'www.smsone.gr' => 'www.smsone.com',
            'www.smsthemall.com' => 'www.smsthemall.com',
        );
        return $gateways;
    }

    public static function getGateway($data) {
        $gateway = null;

        switch ($data['type']) {
            case 'www.ez4usms.com':
                require_once 'ez4usms.class.php';
                $gateway = new Module_TextMe_SmsGateway_Ez4uSms($data);
                break;
            case 'www.smsn.gr':
                require_once 'smsn.class.php';
                $gateway = new Module_TextMe_SmsGateway_Smsn($data);
                break;
            case 'www.smsone.gr':
                require_once 'smsone.class.php';
                $gateway = new Module_TextMe_SmsGateway_SmsOne($data);
                break;
            case 'www.smsthemall.gr':
                require_once 'smsone.class.php';
                $gateway = new Module_TextMe_SmsGateway_SmsThemAll($data);
                break;
            case 'www.example.com':
                require_once 'example.class.php';
                $gateway = new Module_TextMe_SmsGateway_Example($data);
                break;
        }

        return $gateway;
    }
}

abstract class Module_TextMe_SmsGateway {

    public function __construct($data) {

        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->isActive = $data['is_active'];

        $matches = array();

        preg_match_all('/(?<name>[a-zA-Z]\w+):(?<value>\S*)(\s*)*/', $data['data'], $matches);

        foreach ($matches['name'] as $index => $name) {
            $this->$name = $matches['value'][$index];
        }
    }

    /**
     *
     */
    public abstract function getMandatoryParameters($to_string = false);

    /**
     * 
     * @param string $sender
     * @param string $message
     * @param array $recipients
     */
    public abstract function send($sender, $message, $recipients, $time);

    /**
     *
     * @param string $indentifier
     * @param string $mobile
     */
    public abstract function query($identifier, $mobile);

    /**
     *
     *
     */
    public abstract function test();

    /**
     *
     * @return boolean
     */
    public abstract function supportsScheduledMessages();

    /**
     *
     * @return boolean
     */
    public abstract function supportsDeliveryReports();

}

?>
