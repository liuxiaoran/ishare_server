<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/23
 * Time: 12:42
 */
class Add_Location_C extends CI_Controller
{
    private $param_until;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Location_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array('open_id', 'longitude', 'latitude', 'location');
        $param_need_names = array('open_id', 'key', 'longitude', 'latitude', 'location');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data['id'] = $this->Location_m->add($params);
                if ($data['id'] != 0) {
                    $ret = Ret_Factory::create_ret(0, null, $data);
                } else {
                    $ret = Ret_Factory::create_ret(-2);
                }
            }
        }

        echo json_encode($ret);
    }
}