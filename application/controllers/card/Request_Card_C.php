<?php

class Request_Card_C extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
    }

    /*
     * 用户请求卡
     */
    public function index()
    {
        $para_name_array = array('open_id', 'card_id', 'comment', 'rating');
        $paras = $this->get_para($para_name_array);
        $error_message = $this->check_para_index($paras);

        $response = array('status' => -1, 'message' => '');

        /*判断用户是否登录*/
        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($error_message == null) {

        }

        $response['message'] = $error_message;
        echo json_encode($response);
    }

    private function check_para_index($paras)
    {
        $message = null;

        return $message;
    }

    /**
     * 通过参数名称数组获取POST的参数
     * @param $para_name_array
     * @return array
     */
    private function get_para($para_name_array)
    {
        $result = array();

        foreach($para_name_array as $value) {
            $result[$value] = $this->input->post($value);
            if ($result[$value] === false) $result[$value] = null;
        }

        return $result;
    }

    /**
     * 验证用户是否已经登录
     */
    private function is_login()
    {
        $paras = $this->get_para(array('open_id', 'key'));

        return $this->User_m->verify_session_key($paras);
    }

    /**
     * 用户未登陆的返回信息
     */
    private function not_login_response()
    {
        $response['status'] = 2;
        $response['message'] = 'not login';
        echo json_encode($response);
    }
}