<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/28
 * Time: 11:04
 */
class Search_Card_C extends CI_Controller
{

    private $param_until;

    public function __construct() {
        parent::__construct();
        $this->load->model("User_m");
        $this->load->model("Card_m");
        $this->param_until = new Param_Util();
    }

    public function index() {
        Log_Util::log_param($_GET, __CLASS__);

        $param_names = array('keyword', 'longitude', 'latitude', 'page_num', 'page_size');
        $param_need_names = array('open_id', 'key', 'keyword', 'longitude', 'latitude', 'page_num', 'page_size');
        $params = $this->param_until->get_param($param_names, $_GET);
        $message = $this->param_until->check_param($_GET, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_GET)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data = $this->Card_m->search($params['keyword'], $params['longitude'], $params['latitude'], $params['page_num'], $params['page_size']);
                $ret = Ret_Factory::create_ret(0, null, $data);
            }
        }

        echo json_encode($ret);
    }
}