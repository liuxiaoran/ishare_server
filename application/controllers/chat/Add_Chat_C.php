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
        $this->load->model('Record_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_POST)) {
            $para_name_array = array('from_user', 'to_user', 'type', 'content',
                'card_id', 'card_type', 'borrow_id', 'lend_id');
            $data = $this->get_para($para_name_array, $_POST);
            $data['time'] = date('Y-m-d H:i:s', time());

            $message = $this->check_chat_data($data);
            if ($message === null) {
                $chat_name_array = array('from_user', 'to_user', 'type', 'content',
                    'card_id', 'card_type', 'time');
                $chat = $this->get_para($chat_name_array, $data);
                $order = $this->Record_m->query_record($data['card_id'], $data['borrow_id'], $data['lend_id'], $data['card_type']);
                if (!$order) {
                    $record = $this->get_record_data($data);
                    $record['status'] = 0;
                    $chat['order_id'] = $this->Record_m->add($record);
                } else {
                    $chat['order_id'] = $order['id'];
                }
                $chat_id = $this->Chat_m->add_chat($chat);


                if ($chat_id != 0) {
                    $chat['id'] = $chat_id;
                    $chat['to_phone_type'] = $this->User_m->query_phone_type($chat['to_user']);
                    $user = $this->User_m->query_by_id($chat['from_user']);
                    $chat['from_nickname'] = $user['nickname'];
                    $chat['from_gender'] = $user['gender'];
                    $chat['from_avatar'] = $user['avatar'];

                    $this->send_uni_cast($chat);
                    $ret['status'] = 0;
                    $ret['order_id'] = $chat['order_id'];
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

    public function get_para($paras_name_array, $para_array)
    {
        $result = array();
        foreach ($paras_name_array as $para_name) {
            if (isset($para_array[$para_name])) {
                $result[$para_name] = $para_array[$para_name];
            } else {
                $result[$para_name] = null;
            }
        }
        return $result;
    }

    public function get_record_data($data)
    {
        $record = array();
        $record['borrow_id'] = $data['borrow_id'];
        $record['lend_id'] = $data['lend_id'];
        $record['card_id'] = $data['card_id'];
        $record['type'] = $data['card_type'];
        $record['t_create'] = $data['time'];
        return $record;
    }

//    public function add_record($record) {
//        if(!$this->Record_m->is_exist_by_three_id($record['card_id'],
//            $record['type'], $record['borrow_id'], $record['lend_id'])) {
//            $record['status'] = 0;
//            $this->Record_m->add($record);
//        }
//    }

    public function check_chat_data($paras)
    {
        $message = null;
        foreach ($paras as $key => $value) {
            if ($value == null) {
                $message = $key . '不能为空';
            }
        }

        return $message;
    }

    public function send_uni_cast($chat)
    {
        $push = new Push_Util();
        if ($chat['to_phone_type'] == 1) {
            $result = $push->chat_push_android_cast($chat);
        } else {
            $result = $push->chat_push_ios_cast();
        }

        $this->update_chat_status($chat['id'], $result);
    }

    public function send_android_uni_cast($chat)
    {
        $broadcast = new Android_Cast_Util();
        $result = $broadcast->sendUnicast($chat['device_token'], $chat['to_user'], $chat['content']);
        return $result;
    }

    public function send_ios_uni_cast($chat)
    {
        $broadcast = new IOS_Cast_Util();
        $alert = $chat['from_user'] . ':' . $chat['content'];
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