<?php

class Request_Card_C extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Request_card_m');
    }

    /**
     * 用户请求卡
     */
    public function index()
    {
        $para_name_array = array('open_id', 'shop_name', 'shop_location', 'shop_longitude', 'shop_latitude', 'discount',
                                 'ware_type', 'trade_type', 'description', 'user_location', 'user_longitude', 'user_latitude');
        $paras = $this->get_para($para_name_array);
        $error_message = $this->check_para_index($paras);

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

    public function update()
    {
        $para_name_array = array('id', 'shop_name', 'shop_location', 'shop_longitude', 'shop_latitude', 'discount',
                                 'ware_type', 'trade_type', 'description', 'user_location', 'user_longitude', 'user_latitude');
        $paras = $this->get_para($para_name_array);
        $error_message = $this->check_para_update($paras);

        $response = array('status' => -1, 'message' => '');

        /*判断用户是否登录*/
        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($error_message == null) {
            $id = $paras['id'];
            unset($paras['id']);
            foreach($paras as $key => $value) { // 没有传递的参数则保持不变, 故清除之
                if ($value == null) {
                    unset($paras[$key]);
                }
            }

            if ($this->Request_card_m->update($paras, $id)) {
                $response['status'] = 0;
                $response['message'] = 'success';
                echo json_encode($response);
                return;
            } else {
                $response['message'] = 'update failed';
                echo json_encode($response);
                return;
            }

        }

        $response['message'] = $error_message;
        echo json_encode($response);
    }

    /**
     * 用户获取附近的请求借卡信息
     */
    public function get()
    {
        $para_name_array = array('user_longitude', 'user_latitude', 'page_num', 'page_size');
        $paras = $this->get_para($para_name_array);
        $error_message = $this->check_para_get($paras);

        $response = array('status' => -1, 'message' => '');

        if ($paras['page_num'] == null) $paras['num'] = 1;
        if ($paras['page_size'] == null) $paras['size'] = 10;

        /*判断用户是否登录*/
        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($error_message == null) {
                $response['status'] = 0;
                $response['message'] = 'success';
                $response['data'] = $this->Request_card_m->get($paras);
                echo json_encode($response);
                return;
        }

        $response['message'] = $error_message;
        echo json_encode($response);
    }

    public function delete()
    {
        $paras = $this->get_para(array('id'));

        $response = array('status' => -1, 'message' => '');

        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($paras['id'] != null) {
            if ($this->Request_card_m->delete($paras['id'])) {
                $response['status'] = 0;
                $response['message'] = 'success';
                echo json_encode($response);
                return;
            } else {
                $response['message'] = 'delete failed';
                echo json_encode($response);
                return;
            }
        }

        $response['message'] = 'id不能为空';
        echo json_encode($response);
    }

    private function check_para_update($paras)
    {
        $message = null;
        if ($paras['id'] == null) {
            $message = 'id不能为空';
        }

        return $message;
    }

    private function check_para_get($paras)
    {
        $message = null;
        if ($paras['user_longitude'] == null) {
            $message = '用户经度不能为空';
        }elseif ($paras['user_latitude'] == null) {
            $message = '用户纬度不能为空';
        }

        return $message;
    }

    private function check_para_index($paras)
    {
        $message = null;
        if ($paras['open_id'] == null) {
            $message = 'open_id 不能为空';
        } elseif ($paras['shop_name'] == null) {
            $message = '商店名不能为空';
        } elseif ($paras['ware_type'] == null) {
            $message = '商品(卡)类型不能为空';
        } elseif ($paras['trade_type'] == null) {
            $message = '行业类型不能为空';
        }

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