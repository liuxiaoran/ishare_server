<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by IntelliJ IDEA.
 * User: primary
 * Date: 15/7/22
 * Time: 上午10:22
 */
class Get_Service_Info_C extends CI_Controller
{
    private $param_until;

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->param_until = new Param_Util();
    }

    public function index(){
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array('user_openid');
        $param_need_names = array('open_id', 'key', 'user_openid');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data = $this->User_m->login($params['user_openid']);
                $data['date'] = date('Y-m-d H:i:s', time());
                $ret = Ret_Factory::create_ret(0, null, $data);
            }
        }

        echo json_encode($ret);
    }
}