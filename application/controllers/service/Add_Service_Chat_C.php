<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/16
 * Time: 14:22
 */

class Add_Service_Chat_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Customer_service_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_POST)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $param_name_array = array('from_user', 'to_user', 'content');
            $param = $this->get_param($param_name_array);
            $message = $this->check_param($param);
            if ($message === null) {
                $param['time'] = date('Y-m-d H:i:s', time());
                $param['status'] = 0;
                if ($this->Customer_service_m->add_chat($param)) {
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

    public function get_param($param_name_array) {
        $param = array();
        foreach($param_name_array as $param_name) {
            if(isset($_POST[$param_name])) {
                $param[$param_name] = $_POST[$param_name];
            } else {
                $param[$param_name] = $_POST[$param_name];
            }
        }
        return $param;
    }

    public function check_param($paras)
    {
        $message = null;
        foreach ($paras as $key => $value) {
            if ($value == null) {
                $message = $key . '不能为空';
            }
        }
        return $message;
    }
}