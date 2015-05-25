<?php
require_once(dirname(__FILE__) . '/' . 'android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 12:05
 */
class Android_Cast_Util
{
    protected $appkey = NULL;
    protected $appMasterSecret = NULL;
    protected $timestamp = NULL;

    public function __construct($key = "555308aee0f55a3707003fe7", $secret = "p8teunta28aimxxdcrlpudmmdowkxgsd")
    {
        $this->appkey = $key;
        $this->appMasterSecret = $secret;
        $this->timestamp = strval(time());
    }

    public function sendBroadcast($ticker, $title, $text, $after_open = 'go_app', $production_mode = 'true')
    {
        try {
            $broadcast = new AndroidBroadcast();
            $broadcast->setAppMasterSecret($this->appMasterSecret);
            $broadcast->setPredefinedKeyValue("appkey", $this->appkey);
            $broadcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $broadcast->setPredefinedKeyValue("ticker", $ticker);
            $broadcast->setPredefinedKeyValue("title", $title);
            $broadcast->setPredefinedKeyValue("text", $text);
            $broadcast->setPredefinedKeyValue("after_open", $after_open);
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $broadcast->setPredefinedKeyValue("production_mode", $production_mode);
            // [optional]Set extra fields
//            $brocast->setExtraField("test", "helloworld");
//            print("Sending broadcast notification, please wait...\r\n");
//            print("Sent SUCCESS\r\n");
            $result = json_decode($broadcast->send(), true);
            return $result['ret'] == 'SUCCESS' ? true : false;
        } catch (Exception $e) {
            throw new Exception("Android_Cast_Util sendBroadcast exception: " . $e->getMessage());
        }
    }

    function sendUnicast($device_tokens, $title, $text, $ticker = 'ticker',
                         $after_open = 'go_activity', $activity = "com.galaxy.ishare.chat.ChatActivity",
                         $production_mode = 'true')
    {
        try {
            $unicast = new AndroidUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey", $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens", $device_tokens);
            $unicast->setPredefinedKeyValue("ticker", $ticker);
            $unicast->setPredefinedKeyValue("title", $title);
            $unicast->setPredefinedKeyValue("text", $text);
            $unicast->setExtraField("from_phone", $title);
//            $custom['type'] = $type;
//            $custom['time'] = $time;
//            $unicast->setPredefinedKeyValue("custom",           json_encode($custom));
            $unicast->setPredefinedKeyValue("after_open", $after_open);
            $unicast->setPredefinedKeyValue("activity", $activity);
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", $production_mode);
            // Set extra fields
            $unicast->setExtraField("test", "helloworld");
//            print("Sending unicast notification, please wait...\r\n");
//            $unicast->send();
            $result = json_decode($unicast->send(), true);
            Log_Util::log_info($result, __CLASS__);
            return $result['ret'] == 'SUCCESS' ? true : false;
//            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }
    }

}