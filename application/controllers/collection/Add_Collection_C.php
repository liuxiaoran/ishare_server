<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/23
 * Time: 16:01
 */
class Add_Collection_C extends CI_Controller
{
    private $param_until;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Collection_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array('open_id', 'card_id');
        $param_need_names = array('open_id', 'key', 'card_id');
        $params = $this->param_until->get_param($param_names, $_POST);
        $params['time'] = date("Y-m-d H:i:s");
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data['id'] = $this->Collection_m->add($params);
                if ($data['id'] == -3) {
                    $message = 'card has collected';
                    $ret = Ret_Factory::create_ret(-3, $message);
                } else {
                    $ret = Ret_Factory::create_ret(0, null, $data);
                }
            }
        }

        echo json_encode($ret);
    }
}