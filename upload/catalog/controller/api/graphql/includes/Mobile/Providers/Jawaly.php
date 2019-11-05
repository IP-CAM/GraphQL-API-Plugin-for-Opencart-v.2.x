<?php

namespace GQL\Mobile\Providers;

use GQL\Mobile\Contracts\MobileDriverInterface;
use GQL\Mobile\DBManager;

class Jawaly extends MobileDriver implements MobileDriverInterface
{
    private $USER;
    private $PASSWORD;
    private $SENDERNAME;

    /**
     * Populate the credentials required to consume the api
     */
    public function __construct(&$ctx)
    {
        parent::__construct($ctx);
        $this->USER = (new DBManager($ctx))->getSettingByKey('config_mobile','config_mobile_jawaly_username')['value'];
        $this->PASSWORD = (new DBManager($ctx))->getSettingByKey('config_mobile','config_mobile_jawaly_password')['value'];
        $this->SENDERNAME = (new DBManager($ctx))->getSettingByKey('config_mobile','config_mobile_jawaly_sendername')['value'];
    }

    /**
     * Sends message via SMS
     * @param array $mobileNumber
     * @param string $messageContent
     * @return array
     */
    public function sendSMS($mobileNumber, $messageContent)
    {
        $response = [
            'data' => [],
            'errors' => []
        ];
        $telephone = $mobileNumber['country_code'] . $mobileNumber['phone_number'];
        $username = $this->USER;
        $password = $this->PASSWORD;
        $senderName = $this->SENDERNAME;
        $url = "http://www.4jawaly.net/api/sendsms.php?username=$username&password=$password&numbers=$telephone&message=$messageContent&sender=$senderName&unicode=E&return=json";
        $response = json_decode(file_get_contents($url), true);
        if ($response['Code'] == 100) {
            $response['data'][] = [
                'code' => 'DONE',
                'title' => 'Message Sent',
                'content' => 'Message has been sent successfully',
            ];
        } else {
            $response['errors'][] = [
                'code' => 'FAILED',
                'title' => 'Couldn\'t Send',
                'content' => 'An error Occured during Message Send',
            ];
        }
        return $response;
    }
}
