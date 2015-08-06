<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/20
 * Time: 10:23
 */
class Query_Chat_C extends CI_Controller
{
    private $param_until;

    public function __Construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Chat_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array("order_id", "time", "size");
        $param_need_names = array('open_id', 'key', 'order_id', "time", "size");
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $ret['data'] = $this->Chat_m->query($params['order_id'], $params['time'], $params['size']);
                $ret['status'] = 0;
                $ret['message'] = 'success';
            }
        }

        echo json_encode($ret);
    }

}