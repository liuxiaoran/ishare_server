<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/20
 * Time: 10:23
 */
class Query_Chat_C extends CI_Controller
{

    public function __Construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Chat_m');
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_GET)) {
            $user = array_key_exists("user", $_GET) ? $_GET["user"] : null;
            $chat = array_key_exists("chat", $_GET) ? $_GET["chat"] : null;
            $time = array_key_exists("time", $_GET) ? $_GET["time"] : null;
            $size = array_key_exists("size", $_GET) ? $_GET["size"] : null;
            $message = $this->check_query_data($user, $chat, $time, $size);

            if ($message === null) {
                $ret['data'] = $this->Chat_m->query($user, $chat, $time, $size);
                $ret['status'] = 0;
                $ret['message'] = 'success';
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

    public function check_query_data($user, $chat, $time, $size)
    {
        $message = null;
        if ($user === null) {
            $message = 'user不能为空';
        } else if ($chat === null) {
            $message = 'chat不能为空';
        } else if ($time === null) {
            $message = 'time不能为空';
        } else if ($size === null) {
            $message = 'size不能为空';
        }
    }

}