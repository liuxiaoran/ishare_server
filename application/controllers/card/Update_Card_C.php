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
        if (!$this->verify()) {
            $ret = array('status' => '2', 'message' => "not login");
        } else {
            $card = $this->get_card_data();
            $message = $this->check_card_data($card);
            if (empty($message)) {
                if ($this->Card_m->updateCard($card)) {
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

    public function verify()
    {
        $phone = array_key_exists("phone", $_POST) ? $_POST["phone"] : null;
        $key = array_key_exists("key", $_POST) ? $_POST["key"] : null;

        if ($this->User_m->verifyKey($phone, $key)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_card_data()
    {
        $data = array();
        $data['owner'] = $phone = array_key_exists("owner", $_POST) ? $_POST["owner"] : null;
        $data['shop_name'] = array_key_exists("shop_name", $_POST) ? $_POST["shop_name"] : null;
        $data['ware_type'] = array_key_exists("ware_type", $_POST) ? $_POST["ware_type"] : null;
        $data['discount'] = array_key_exists("discount", $_POST) ? $_POST["discount"] : null;
        $data['trade_type'] = array_key_exists("trade_type", $_POST) ? $_POST["trade_type"] : null;
        $data['shop_location'] = array_key_exists("shop_location", $_POST) ? $_POST["shop_location"] : null;
        $data['owner_available'] = array_key_exists("owner_available", $_POST) ? $_POST["owner_available"] : null;
        $data['description'] = array_key_exists("description", $_POST) ? $_POST["description"] : null;
        $data['img'] = array_key_exists("img", $_POST) ? $_POST["img"] : null;
        $data['share_type'] = array_key_exists("share_type", $_POST) ? $_POST["share_type"] : null;

        return $data;
    }

    public function check_card_data($data)
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