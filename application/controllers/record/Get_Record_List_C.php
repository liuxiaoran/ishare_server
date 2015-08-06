<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/26
 * Time: 16:15
 */
class Get_Record_List_C extends CI_Controller
{
    private $param_until;

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Record_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $param_names = array('open_id', 'longitude', 'latitude', 'page_size', 'page_num');
        $param_need_names = array('open_id', 'key', 'longitude', 'latitude', 'page_size', 'page_num');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data = $this->Record_m->get_order($params['open_id'], $params['longitude'], $params['latitude'], $params['page_size'], $params['page_num']);
                $ret = Ret_Factory::create_ret(0, null, $data);
            }
        }

        echo json_encode($ret);
    }
}