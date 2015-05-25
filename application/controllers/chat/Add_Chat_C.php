<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
include_once(dirname(__FILE__) . '/../../util/Push_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:54
 */
class Add_Chat_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Chat_m');
    }


    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->verify()) {
            $chat = $this->get_chat_data();
            $message = $this->check_chat_data($chat);
            if ($message === null) {
                $chat_id = $this->Chat_m->add_chat($chat);
                $chat['id'] = $chat_id;
                $phone = $this->User_m->query_user_phone($chat['to_phone']);
                $chat['phone_type'] = $phone['phone_type'];
                if ($chat_id != 0) {
                    $this->send_uni_cast($chat);
                    $ret['status'] = 0;
                    $ret['message'] = 'success';
                } else {
                    $ret['status'] = -1;
                    $ret['message'] = 'failure';
                }
            } else {
                $ret['status'] = -1;
                $ret['message'] = $message;
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_chat_data()
    {
        $data['from_phone'] = array_key_exists("from_phone", $_POST) ? $_POST["from_phone"] : null;
        $data['to_phone'] = array_key_exists("to_phone", $_POST) ? $_POST["to_phone"] : null;
        $data['type'] = array_key_exists("type", $_POST) ? $_POST["type"] : null;
        $data['content'] = array_key_exists("content", $_POST) ? $_POST["content"] : null;
        $data['time'] = $this->get_cur_time();

        return $data;
    }

    public function check_chat_data($data)
    {
        $message = null;
        if ($data['from_phone'] === null) {
            $message = 'from_phone不能为空';
        } else if ($data['to_phone'] === null) {
            $message = 'to_phone不能为空';
        } else if ($data['type'] === null) {
            $message = 'type不能都为空';
        } else if ($data['content'] === null) {
            $message = 'content不能为空';
        } else if ($data['time'] === null) {
            $message = 'time不能为空';
        }
        return $message;
    }

    public function verify()
    {
        $phone = array_key_exists("phone", $_POST) ? $_POST["phone"] : null;
        $key = array_key_exists("key", $_POST) ? $_POST["key"] : null;

        return $this->User_m->verify_session_key($phone, $key);
    }

    public function send_uni_cast($chat)
    {
        $push = new Push_Util();
        if ($chat['phone_type'] == 1) {
            $result = $push->push_android_cast($chat['to_phone'], $chat['from_phone'], $chat['content'], $chat['type'], $chat['time']);
        } else {
            $result = $push->push_ios_cast();
        }

        $this->update_chat_status($chat['id'], $result);
    }

    public function send_android_uni_cast($chat)
    {
        $broadcast = new Android_Cast_Util();
        $result = $broadcast->sendUnicast($chat['device_token'], $chat['from_phone'], $chat['content']);
        return $result;
    }

    public function send_ios_uni_cast($chat)
    {
        $broadcast = new IOS_Cast_Util();
        $alert = $chat['from_phone'] . ':' . $chat['content'];
        $result = $broadcast->sendUnicast($chat['device_token'], $alert, '', '');
        return $result;
    }

    public function update_chat_status($id, $result)
    {
        if ($result) {
            $this->Chat_m->update_status($id, 1);
        }
    }

    public function get_cur_time()
    {
        return date("Y-m-d H:i:s");
    }
}