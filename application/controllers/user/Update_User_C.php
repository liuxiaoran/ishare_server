<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:11
 */
class Update_User_C extends CI_Controller
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
        if (!$this->User_m->verify_session_key($_POST)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $user = $this->get_data();
            $message = $this->check_data($user);
            if ($message == null) {
                if ($this->User_m->update_user_info($user)) {
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
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_data()
    {
        $user['open_id'] = array_key_exists("open_id", $_POST) ? $_POST["open_id"] : null;
        $user['phone'] = array_key_exists("phone", $_POST) ? $_POST["phone"] : null;
        $user['nickname'] = array_key_exists("nickname", $_POST) ? $_POST["nickname"] : null;
        $user['avatar'] = array_key_exists("avatar", $_POST) ? $_POST["avatar"] : null;
        $user['gender'] = array_key_exists("gender", $_POST) ? $_POST["gender"] : null;

        return $user;
    }

    public function check_data($data)
    {
        $message = null;
        if ($data['phone'] == null) {
            $message = 'phone不能为空';
        }
        return $message;
    }
}