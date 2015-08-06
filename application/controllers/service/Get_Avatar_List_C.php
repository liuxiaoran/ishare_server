<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/24
 * Time: 19:15
 */
class Get_Avatar_List_C extends CI_Controller
{
    private $param_until;

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Customer_service_m');
        $this->param_until = new Param_Util();
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array('size', 'server_openid');
        $param_need_names = array('open_id', 'key', 'size', 'server_openid');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data = $this->Customer_service_m->get_avatar($params['size'], $params['server_openid']);
                $ret = Ret_Factory::create_ret(0, null, $data);
            }
        }

        echo json_encode($ret);
    }

}