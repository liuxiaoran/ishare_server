<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by IntelliJ IDEA.
 * User: primary
 * Date: 15/7/22
 * Time: 上午10:22
 */

class Get_Service_Info_C  extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
    }

    public function index(){
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        $param_name_array = array('user_openid');
        $param = $this->get_param($param_name_array);
        $message = $this->check_param($param);
        if ($message === null) {
            $ret['data'] = $this->User_m->login($param['user_openid']);
            $ret['status'] = 0;
            $ret['message'] = 'success';
            $ret['date'] = date('Y-m-d H:i:s',time());
        } else {
            $ret['status'] = -1;
            $ret['message'] = $message;
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }
    //当参数为空字符串的时候赋为NULL值
    public function get_param($param_name_array) {
        $param = array();
        foreach($param_name_array as $param_name) {
            if(isset($_POST[$param_name])) {
                $param[$param_name] = $_POST[$param_name];
            } else {
                $param[$param_name] = null;
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