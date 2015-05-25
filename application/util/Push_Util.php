<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/23
 * Time: 13:39
 */

use JPush\Model as M;
use JPush\JPushClient;
use JPush\JPushLog;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;


class Push_Util
{

    protected $app_key = '';
    protected $master_secret = '';
    protected $client = '';

    public function __construct($key = "745a8e1fdefd28e07578b984", $secret = "2d11a066a77aab3662269e80")
    {
        $this->app_key = $key;
        $this->master_secret = $secret;
        JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
        $this->client = new JPushClient($key, $secret);
    }

    public function push_android_cast($alias, $title, $text, $type, $time)
    {
        $status = true;
        try {
            $result = $this->client->push()
                ->setPlatform(M\Platform('android'))
                ->setAudience(M\Audience(M\alias(array($alias))))
                ->setNotification(M\notification('Hi, JPush',
                    M\android($text, $title, 1, array("type" => $type, "time" => $time))
                ))
                ->setMessage(M\message('Message Content', 'Message Title', 'Message Type', array("type" => $type, "time" => $time)))
                ->printJSON()
                ->send();

        } catch (APIRequestException $e) {
            $status = false;
        } catch (APIConnectionException $e) {
            $status = false;
        }

        return $status;
    }

    public function push_ios_cast()
    {
        $status = true;
        try {
            $result = $this->client->push()
                ->setPlatform(M\Platform('android'))
                ->setAudience(M\Audience(M\alias(array('18811791727'))))
                ->setNotification(M\notification('Hi, JPush',
                    M\android('Hi, Android', 'Message Title', 1, array("key1" => "value1", "key2" => "value2"))
                ))
                ->setMessage(M\message('Message Content', 'Message Title', 'Message Type', array("key1" => "value1", "key2" => "value2")))
                ->printJSON()
                ->send();

        } catch (APIRequestException $e) {
            $status = false;
        } catch (APIConnectionException $e) {
            $status = false;
        }

        return $status;
    }
}