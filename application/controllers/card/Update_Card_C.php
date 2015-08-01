<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:02
 */
class Update_Card_C extends CI_Controller
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
            $param_name = array('id', 'owner', 'shop_name', 'shop_longitude', 'shop_latitude',
                'ware_type', 'discount', 'service_charge', 'trade_type', 'shop_location',
                'description', 'img');
            $card = $this->get_para($param_name, $_POST);
            $message = $this->check_card_data($card);
            if (empty($message)) {
                if ($this->Card_m->update_card($card)) {
                    $ret['status'] = 0;
                    $ret['message'] = 'success';
                } else {
                    $ret['status'] = -1;
                    $ret['message'] = 'exe sql error';
                }
            } else {
                $ret = array('status' => -1, "message" => $message);
                $ret['status'] = -1;
                $ret['message'] = $message;
            }
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_para($paras_name_array, $para_array)
    {
        $result = array();
        foreach ($paras_name_array as $para_name) {
            if (isset($para_array[$para_name])) {
                $result[$para_name] = $para_array[$para_name];
            }
        }
        return $result;
    }

    public function check_card_data($data)
    {
        $message = null;
        if ($data['id'] == null) {
            $message = 'id is null';//'手机号不能为空';
        }

        return $message;
    }
}