<?php

class Comment_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Comment_m');
    }

    /**
     * 用户添加评论
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
            if ($this->Comment_m->add($paras)) {
                $response['status'] = 0;
                $response['message'] = 'success';
                echo json_encode($response);
                return;
            }
        }

        $response['message'] = $error_message;
        echo json_encode($response);
    }

    public function get()
    {
        $para_name_array = array('card_id', 'page_num', 'page_size');
        $paras = $this->get_para($para_name_array);
        $error_message = $this->check_para_get($paras);

        if ($paras['page_num'] == null) $paras['page_num'] = 1;
        if ($paras['page_size'] == null) $paras['page_size'] = 10;

        $response = array('status' => -1, 'message' => '');

        /*判断用户是否登录*/
        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($error_message == null) {
            $response['status'] = 0;
            $response['message'] = 'success';
            $response['data'] = $this->Comment_m->get($paras);
            echo json_encode($response);
            return;
        }

        $response['message'] = $error_message;
        echo json_encode($response);
    }

    private function check_para_get($paras)
    {
        $message = null;

        if ($paras['card_id'] == null)  $message = "card_id 不能为空";

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
     * 判断index方法中用户传递的参数是否合法
     * @param $paras
     * @return null|string
     */
    private function check_para_index($paras)
    {
        $message = null;

        foreach($paras as $key => $value) {
            if ($value == null && $key != 'comment') // 评论内容可以为空
                $message = $key . "不能为空";
        }

        if (($message == null) && ($paras['rating'] < 0 || $paras['rating'] > 5)) // 评分值必须在0-5分之间
            $message = "评分值必须在0-5分之间";

        return $message;
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