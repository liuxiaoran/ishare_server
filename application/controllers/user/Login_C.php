<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:07
 */
class Login_C extends CI_Controller
{
    private $param_until;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $param_names = array('open_id', 'phone_type');
        $param_need_names = array('open_id', 'phone_type');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            $data = $this->User_m->login($params['open_id']);
            $this->User_m->update_user_info($params);
            $data['key'] = $this->User_m->get_session_key($params['open_id']);
            if ($data == null) {
                $this->User_m->add_user($params);
            }
            $ret = Ret_Factory::create_ret(0, null, $data);
        }
        echo json_encode($ret);
    }
}