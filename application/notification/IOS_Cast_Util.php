<?php
require_once(dirname(__FILE__) . '/' . 'ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'ios/IOSUnicast.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 12:42
 */
class IOS_Cast_Util
{
    protected $appkey = NULL;
    protected $appMasterSecret = NULL;
    protected $timestamp = NULL;

    public function __construct($key = 0, $secret = 0)
    {
        $this->appkey = $key;
        $this->appMasterSecret = $secret;
        $this->timestamp = strval(time());
    }

    function sendBroadcast($alert, $badge, $sound, $production_mode = 'true')
    {
        try {
            $broadcast = new IOSBroadcast();
            $broadcast->setAppMasterSecret($this->appMasterSecret);
            $broadcast->setPredefinedKeyValue("appkey", $this->appkey);
            $broadcast->setPredefinedKeyValue("timestamp", $this->timestamp);

            $broadcast->setPredefinedKeyValue("alert", $alert);
            $broadcast->setPredefinedKeyValue("badge", $badge);
            $broadcast->setPredefinedKeyValue("sound", $sound);
            // Set 'production_mode' to 'true' if your app is under production mode
            $broadcast->setPredefinedKeyValue("production_mode", $production_mode);
            // Set customized fields
//            $broadcast->setCustomizedField("test", "helloworld");
//            print("Sending broadcast notification, please wait...\r\n");
            $result = json_decode($broadcast->send(), true);
            return $result['ret'] == 'SUCCESS' ? true : false;
//            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
        throw new Exception("IOS_Cast_Util sendBroadcast exception: " . $e->getMessage());
    }

    function sendUnicast($device_tokens, $alert, $badge, $sound, $production_mode = 'true')
    {
        try {
            $unicast = new IOSUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey", $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens", $device_tokens);
            $unicast->setPredefinedKeyValue("alert", $alert);
            $unicast->setPredefinedKeyValue("badge", $badge);
            $unicast->setPredefinedKeyValue("sound", $sound);
            // Set 'production_mode' to 'true' if your app is under production mode
            $unicast->setPredefinedKeyValue("production_mode", $production_mode);
            // Set customized fields
            $unicast->setCustomizedField("test", "helloworld");
//            print("Sending unicast notification, please wait...\r\n");
            $result = json_decode($unicast->send(), true);
            return $result['ret'] == 'SUCCESS' ? true : false;
//            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            throw new Exception("IOS_Cast_Util sendUnicast exception: " . $e->getMessage());
        }
    }
}