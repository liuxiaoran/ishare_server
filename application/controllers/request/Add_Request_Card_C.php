<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by Intellij.
 * User: 源初
 * Date: 2015/7/25
 * Time: 20:31
 */

class Add_Request_Card_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Request_card_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);
        $para_name_array = array('open_id', 'shop_name', 'shop_location', 'shop_longitude', 'shop_latitude', 'discount',
            'ware_type', 'trade_type', 'description', 'user_longitude', 'user_latitude');
        $paras = $this->get_para($para_name_array);
        $error_message = $this->check_para($paras);

        $response = array('status' => -1, 'message' => '');

        /*判断用户是否登录*/
        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($error_message == null) {
            if ($this->Request_card_m->add($paras)) {
                $response['status'] = 0;
                $response['message'] = 'success';
                echo json_encode($response);
                return;
            }
        }

        $response['message'] = $error_message;
        echo json_encode($response);
    }

    private function get_para($para_name_array)
    {
        $result = array();

        foreach($para_name_array as $value) {
            $result[$value] = $this->input->post($value);
            if ($result[$value] === false) $result[$value] = null;
        }

        return $result;
    }

    private function check_para($paras)
    {
        $message = null;
        if ($paras['open_id'] == null) {
            $message = 'open_id 不能为空';
        } elseif ($paras['shop_name'] == null) {
            $message = '商店名不能为空';
        } //elseif ($paras['ware_type'] == null) {
        //$message = '商品(卡)类型不能为空';}
        elseif ($paras['trade_type'] == null) {
            $message = '行业类型不能为空';
        }

        return $message;
    }

    private function is_login()
    {
        $paras = $this->get_para(array('open_id', 'key'));

        return $this->User_m->verify_session_key($paras);
    }

    private function not_login_response()
    {
        $response['status'] = 2;
        $response['message'] = 'not login';
        echo json_encode($response);
    }

}