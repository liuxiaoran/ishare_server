<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
include_once(dirname(__FILE__) . '/../../util/Push_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:54
 */
class Add_Chat_C extends CI_Controller
{
    private $param_until;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Chat_m');
        $this->load->model('Record_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array('from_user', 'to_user', 'type', 'content', 'card_id', 'card_type', 'borrow_id', 'lend_id');
        $param_need_names = array('open_id', 'key', 'from_user', 'to_user', 'type', 'content', 'card_id', 'card_type', 'borrow_id', 'lend_id');
        $params = $this->param_until->get_param($param_names, $_POST);
        $params['time'] = date('Y-m-d H:i:s', time());
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $order = $this->Record_m->query_record($params['card_id'], $params['borrow_id'], $params['lend_id'], $params['card_type']);
                if (!$order) {
                    $record = $this->get_record_data($params);
                    $params['order_id'] = $this->Record_m->add($record);
                } else {
                    $params['order_id'] = $order['id'];
                }

                $chat_id = $this->Chat_m->add_chat($params);
                if ($chat_id != 0) {
                    $chat = $this->get_chat_data($params, $chat_id);
                    $this->send_uni_cast($chat);
                    $data['order_id'] = $chat['order_id'];
                    $ret = Ret_Factory::create_ret(0, null, $data);
                } else {
                    $ret = Ret_Factory::create_ret(-2);
                }
            }
        }

        echo json_encode($ret);
    }

    public function get_chat_data($chat, $chat_id)
    {
        $chat['id'] = $chat_id;
        $phone = $this->User_m->query_phone_type($chat['to_user']);
        $chat['to_phone_type'] = $phone['phone_type'];
        $user = $this->User_m->query_by_id($chat['from_user']);
        $chat['from_nickname'] = $user['nickname'];
        $chat['from_gender'] = $user['gender'];
        $chat['from_avatar'] = $user['avatar'];
        return $chat;
    }

    public function get_record_data($data)
    {
        $record = array();
        $record['borrow_id'] = $data['borrow_id'];
        $record['lend_id'] = $data['lend_id'];
        $record['card_id'] = $data['card_id'];
        $record['type'] = $data['card_type'];
        $record['t_create'] = $data['time'];
        $record['status'] = 0;
        return $record;
    }

    public function send_uni_cast($chat)
    {
        $push = new Push_Util();
        if ($chat['to_phone_type'] == 1) {
            $result = $push->chat_push_android_cast($chat);
        } else {
            $result = $push->chat_push_ios_cast();
        }

        if ($result) {
            $this->Chat_m->update_status($chat['id'], 1);
        }
    }
}