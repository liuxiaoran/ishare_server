<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
include_once(dirname(__FILE__) . '/../../util/Push_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/16
 * Time: 14:22
 */
class Add_Service_Chat_C extends CI_Controller
{
    private $param_until;
    private $ishare = 'oyIsQt8l9QupElMamo7Ww6ixk1FE';

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Customer_service_m');
        $this->param_until = new Param_Util();
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);

        $param_names = array('from_user', 'to_user', 'content');
        $param_need_names = array('open_id', 'key', 'from_user', 'to_user', 'content');
        $params = $this->param_until->get_param($param_names, $_POST);
        $params['time'] = date('Y-m-d H:i:s', time());
        $params['status'] = 0;
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $chat_id = $this->Customer_service_m->add_chat($params);
                if ($chat_id != 0) {
                    $params['id'] = $chat_id;

                    if ($params['from_user'] == $this->ishare) {
                        $chat['to_phone_type'] = $this->User_m->query_phone_type($params['to_user']);
                        $this->send_service_cast($chat);
                    }
                    $ret = Ret_Factory::create_ret(0);
                } else {
                    $ret = Ret_Factory::create_ret(-2);
                }
            }
        }

        echo json_encode($ret);
    }

    public function send_service_cast($chat)
    {
        $push = new Push_Util();
        if ($chat['to_phone_type'] == 1) {
            $result = $push->push_android_service($chat);
        } else {
            $result = $push->push_android_service($chat);
        }

        $this->update_chat_status($chat['id'], $result);
    }
}