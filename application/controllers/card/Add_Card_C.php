<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 10:59
 */
class Add_Card_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_POST)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $param_names = array('owner', 'shop_name', 'ware_type', 'discount',
                'service_charge', 'trade_type', 'shop_location', 'shop_longitude',
                'shop_latitude', 'description', 'img', 'location_id');
            $data = $this->get_param($param_names, $_POST);
            $message = $this->check_card_data($data);

            if ($message === null) {
                if ($this->Card_m->add_card($data)) {
                    $ret['status'] = 0;
                    $ret['message'] = 'success';
                } else {
                    $ret['status'] = -1;
                    $ret['message'] = 'failure';
                }
            } else {
                $ret['status'] = -1;
                $ret['message'] = $message;
            }
        }

        echo json_encode($ret);
    }

    public function get_param($param_names, $params)
    {
        $result = array();
        foreach ($param_names as $para_name) {
            if (isset($params[$para_name])) {
                $result[$para_name] = $params[$para_name];
            } else {
                $result[$para_name] = null;
            }
        }
        return $result;
    }

    //todo:检查数据类型和折扣，服务费等
    public function check_param($data)
    {
        $message = null;
        if ($data['owner'] == null) {
            $message = 'owner is null';//'手机号不能为空';
        } else if ($data['shop_name'] == null) {
            $message = 'shop_name is null';//'店的名称不能为空';
        } else if ($data['ware_type'] == null) {
            $message = 'ware_type is null';//'卡的类型不能为空';
        } else if ($data['trade_type'] == null) {
            $message = 'trade_type is null';//'行业类型不能为空';
        } else if ($data['shop_location'] == null) {
            $message = 'shop_location is null';//'商店位置不能为空';
        }

        return $message;
    }

}