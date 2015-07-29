<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by Intellij.
 * User: 源初
 * Date: 2015/7/25
 * Time: 20:31
 */

class Get_Request_Card_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Request_card_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);
        $para_name_array = array('user_longitude', 'user_latitude', 'page_num', 'page_size');
        $paras = $this->get_para($para_name_array);
        $error_message = $this->check_para($paras);

        $response = array('status' => -1, 'message' => '');

        if ($paras['page_num'] == null) $paras['page_num'] = 1;
        if ($paras['page_size'] == null) $paras['page_size'] = 10;

        /*判断用户是否登录*/
        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($error_message == null) {
            $response['status'] = 0;
            $response['message'] = 'success';
            $response['data'] = $this->Request_card_m->get($paras['user_longitude'],$paras['user_latitude'],$paras['page_num'],$paras['page_size']);
            echo json_encode($response);
            return;
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
        if ($paras['user_longitude'] == null) {
            $message = '用户经度不能为空';
        }elseif ($paras['user_latitude'] == null) {
            $message = '用户纬度不能为空';
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