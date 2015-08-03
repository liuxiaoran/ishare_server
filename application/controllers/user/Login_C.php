<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:07
 */
class Login_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        $user = $this->get_user_data();
        $message = $this->check_user_data($user);
        if ($message == null) {
            $data = $this->User_m->login($user['open_id']);
            if ($data == null) {
                $this->User_m->add_user($user);
            }
            $this->User_m->update_user_info($user);
            $data['key'] = $this->User_m->get_session_key($user['open_id']);
            $ret['status'] = 0;
            $ret['message'] = 'success';
            $ret['data'] = $data;
        } else {
            $ret['status'] = -1;
            $ret['message'] = $message;
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_user_data()
    {
        $user['open_id'] = array_key_exists("open_id", $_POST) ? $_POST["open_id"] : null;
        $user['phone_type'] = array_key_exists("phone_type", $_POST) ? $_POST["phone_type"] : null;

        return $user;
    }

    public function check_user_data($user)
    {
        $message = null;
        if ($user['open_id'] == null) {
            $message = 'open_id 不能为空';
        } else if ($user['phone_type'] == null) {
            $message = 'phone_type 不能为空';
        }
        return $message;
    }
}