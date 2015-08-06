<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 10:59
 */
class Add_Card_C extends CI_Controller
{
    private $param_until;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array('owner', 'shop_name', 'ware_type', 'discount',
            'service_charge', 'trade_type', 'shop_location', 'shop_longitude',
            'shop_latitude', 'description', 'img', 'location_id');
        $param_need_names = array('open_id', 'key', 'owner', 'shop_name',
            'ware_type', 'discount', 'service_charge', 'trade_type',
            'shop_location', 'shop_longitude', 'shop_latitude', 'location_id');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                if ($this->Card_m->add_card($params)) {
                    $ret = Ret_Factory::create_ret(0);
                } else {
                    $ret = Ret_Factory::create_ret(-2);
                }
            }
        }
        echo json_encode($ret);
    }
}