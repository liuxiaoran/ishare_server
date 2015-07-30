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
    protected $app_key = '745a8e1fdefd28e07578b984';
    protected $master_secret = '2d11a066a77aab3662269e80';
    protected $client;

    public function __construct()
    {
        JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
        $this->client = new JPushClient($this->app_key, $this->master_secret);
    }

    //, md5($chat['to_user']), $chat['nickname'], $chat['content'],$chat['from_user'], $chat['order_id'], $chat['type'], $chat['time']
    public function chat_push_android_cast($chat)
    {
        try {
            $status = $this->client->push()
                ->setPlatform(M\Platform('android'))
                ->setAudience(M\Audience(M\alias(array(md5($chat['to_user'])))))
                ->setNotification(M\notification('Hi, JPush',
                    M\android($chat['content'], $chat['from_nickname'], 1,
                        array("from_gender" => $chat['from_gender'], "from_user" => $chat['from_user'],
                            "from_avatar" => $chat['from_avatar'], "type" => $chat['type'], "time" => $chat['time'],
                            "order_id" => $chat['order_id'], "card_id" => $chat['card_id'], "card_type" => $chat['card_type']))
                ))
                ->setMessage(M\message('Message Content', 'Message Title', 'Message Type', array()))
//                ->printJSON()
                ->send();
  
        } catch (APIRequestException $e) {
            $status = false;
        } catch (APIConnectionException $e) {
            $status = false;
        }

        return $status;
    }

    public function chat_push_ios_cast()
    {
        try {
            $status = $this->client->push()
                ->setPlatform(M\Platform('android'))
                ->setAudience(M\Audience(M\alias(array('18811791727'))))
                ->setNotification(M\notification('Hi, JPush',
                    M\android('Hi, Android', 'Message Title', 1, array("key1" => "value1", "key2" => "value2"))
                ))
                ->setMessage(M\message('Message Content', 'Message Title', 'Message Type', array("key1" => "value1", "key2" => "value2")))
//                ->printJSON()
                ->send();

        } catch (APIRequestException $e) {
            $status = false;
        } catch (APIConnectionException $e) {
            $status = false;
        }

        return $status;
    }

    public function push_android_record($open_id, $title, $content, $from_user, $from_gender, $from_avatar, $time, $orderId)
    {
        try {
            $status = $this->client->push()
                ->setPlatform(M\Platform('android'))
                ->setAudience(M\Audience(M\alias(array(md5($open_id)))))
                ->setNotification(M\notification('Hi, JPush',
                    M\android($content, $title, 1,
                        array("from_gender" => $from_gender, "from_user" => $from_user,
                            "from_avatar" => $from_avatar, "order_id" => $orderId, "type" => 0, "time" => $time))))
                ->setMessage(M\message('Message Content', 'Message Title', 'Message Type', array()))
//                ->printJSON()
                ->send();

        } catch (APIRequestException $e) {
            $status = false;
        } catch (APIConnectionException $e) {
            $status = false;
        }

        return $status;
    }
}