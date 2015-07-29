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
            $param_names = array('open_id', 'phone', 'nickname', 'avatar', 'gender');
            $user = $this->get_data($param_names);
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

    public function get_data($param_names)
    {
        $result = array();
        foreach ($param_names as $para_name) {
            if (isset($_POST[$para_name])) {
                $result[$para_name] = $_POST[$para_name];
            }
        }
        return $result;
    }

    public function check_data($data)
    {
        $message = null;
        if ($data['open_id'] == null) {
            $message = 'open_id不能为空';
        }
        return $message;
    }
}