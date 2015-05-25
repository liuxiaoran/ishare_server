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
            if (!$this->User_m->login($user)) {
                $ret['status'] = -1;
                $ret['message'] = 'password error';
            } else {
                $ret['status'] = 0;
                $ret['key'] = $this->User_m->get_session_key($user['phone']);
                $ret['message'] = 'success';
            }
        } else {
            $ret['status'] = -1;
            $ret['message'] = $message;
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_user_data()
    {
        $user['phone'] = array_key_exists("phone", $_POST) ? $_POST["phone"] : null;
        $user['pw'] = array_key_exists("password", $_POST) ? $_POST["password"] : null;

        return $user;
    }

    public function getData()
    {
        $result = '';
        foreach ($_POST as $key => $value) {
            $result = $result . $key . '=' . $value . '&';
        }
        return $result;
    }

    public function check_user_data($user)
    {
        $message = null;
        if ($user['phone'] == null) {
            $message = 'phone 不能为空';
        } else if ($user['pw'] == null) {
            $message = 'pw 不能为空';
        }
        return $message;
    }
}