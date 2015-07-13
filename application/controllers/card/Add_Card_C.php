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
            $data = $this->get_card_data();
            $message = $this->check_card_data($data);
//            $card = $this->transform_data($data);
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

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_card_data()
    {
        $data = array();
        $data['owner'] = array_key_exists("owner", $_POST) ? $_POST["owner"] : null;
        $data['shop_name'] = array_key_exists("shop_name", $_POST) ? $_POST["shop_name"] : null;
        $data['ware_type'] = array_key_exists("ware_type", $_POST) ? $_POST["ware_type"] : null;
        $data['discount'] = array_key_exists("discount", $_POST) ? $_POST["discount"] : null;
        $data['trade_type'] = array_key_exists("trade_type", $_POST) ? $_POST["trade_type"] : null;
        $data['shop_location'] = array_key_exists("shop_location", $_POST) ? $_POST["shop_location"] : null;
        $data['shop_longitude'] = array_key_exists("shop_longitude", $_POST) ? $_POST["shop_longitude"] : null;
        $data['shop_latitude'] = array_key_exists("shop_latitude", $_POST) ? $_POST["shop_latitude"] : null;
        $data['owner_available'] = array_key_exists("owner_available", $_POST) ? $_POST["owner_available"] : null;
        $data['description'] = array_key_exists("description", $_POST) ? $_POST["description"] : null;
        $data['img'] = array_key_exists("img", $_POST) ? $_POST["img"] : null;
//        $data['img'] = $this->trans_image($data['img']);

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

    public function trans_img($img_str)
    {
        $result = '';

        $img_str = substr($img_str, 1, sizeof($img_str) - 2);
        $images = explode(",", $img_str);

        for ($i = 0; $i < sizeof($images); $i++) {
            $images[$i] = substr($images[$i], 1, sizeof($images[$i]) - 2);
            if ($i == sizeof($images) - 1) {
                $result = $result . $images[$i];
            } else {
                $result = $result . $images[$i] . ",";
            }

        }

        return $result;
    }

    public function trans_image($img_json)
    {
        $result = "['";
        $images = (Array)json_decode($img_json);
        for ($i = 0; $i < sizeof($images); $i++) {
            if ($i == sizeof($images) - 1) {
                $result = $result . $images[$i] . "']";
            } else {
                $result = $result . $images[$i] . "',";
            }

        }

        return $result;
    }
}