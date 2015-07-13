<?php
include_once(dirname(__FILE__) . '/../../util/Push_Util.php');
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

class Record_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Record_m');
        $this->load->model('User_m');
    }

    /**
     * 添加一条借卡记录
     */
    public function add()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $para_name_array = array('borrow_id', 'lend_id', 'card_id', 'type');
        $record = $this->get_para($para_name_array);
        $record['status'] = 2;
        $record['t_apply'] = date("Y-m-d H:i:s");
        $message = $this->check_para_add($record);

        $response = array('status' => -1, 'message' => '');

        if (!$this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($message == null) {
            if ($record['borrow_id'] == $record['lend_id']) {
                $response['status'] = 1;
                $response['message'] = 'borrow_id不能与lend_id相同';
            } elseif ($id = $this->Record_m->add($record)) {
                $response['id'] = $id;
                $response['status'] = 0;
                $response['message'] = 'success';
            }
        } else {
            $response['status'] = -1;
            $response['message'] = $message;
        }

        echo json_encode($response);
    }

    /**
     * 获取用户的借卡记录
     */
    public function get()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $para_name_array = array('borrow_id', 'lend_id', 'type', 'longitude', 'latitude', 'page_size', 'page_num');
        $paras = $this->get_para($para_name_array);
        $message = $this->check_borrow_lend_id($paras);

        if ($paras['page_num'] == null) $paras['page_num'] = 1; // 设置页码的默认值
        if ($paras['page_size'] == null) $paras['page_size'] = 10;

        $response = array('status' => -1, 'message' => '');

        if (!$this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($message == null) {
            $response['data'] = $this->Record_m->get_order($paras);
            $response['status'] = 0;
            $response['message'] = 'success';
        } else {
            $response['status'] = -1;
            $response['message'] = $message;
        }

        echo json_encode($response);
    }

    public function get_by_id()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $paras = $this->get_para(array('id'));

        $response = array('status' => -1, 'message' => '');

        if (!$this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($paras['id'] != null) {
            $response['status'] = 0;
            $response['message'] = 'success';
            $response['data'] = $this->Record_m->get_by_id($paras['id']);
            echo json_encode($response);
            return;
        }

        $response['message'] = 'id不能为空';
        echo json_encode($response);
    }

    public function is_exist_with_card()
    {
        Log_Util::log_param($_POST, __CLASS__);
        $paras = $this->get_para(array('card_id', 'borrow_id', 'lend_id'));
        $error_message = $this->check_para_is_exist($paras);

        $response = array('status' => -1, 'message' => '');

        if (!$this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($error_message == null) {
            if ($data = $this->Record_m->is_exist_by_three_id($paras['card_id'], $paras['borrow_id'], $paras['lend_id'])) {
                $response['status'] = 0;
                $response['message'] = 'success';
                $response['data'] = $data;
            } else {
                $response['data'] = $this->Record_m->query_with_three_id($paras['card_id'], $paras['borrow_id'], $paras['lend_id']);
                $response['status'] = 1;
                $response['message'] = 'no such record';
            }
            echo json_encode($response);
            return;
        }

        $response['message'] = $error_message;
        echo json_encode($response);
    }

    /**
     * 更新借卡记录的状态, 1=申请借卡, 2=取消申请, 3=未归还(确认拿卡), 4=借卡人还卡, 5=借卡人还款,
     *                  -1=同意借卡, -2=拒绝借卡, -3=卡主借出卡, -4=未付款(确认还卡), -5=卡主确认收款
     *                  0=意外结束交易
     *
     * 初步修改借卡记录的状态 0=意外结束交易, 1=请求借卡过程, 2=同意借卡, 3=已还卡, 4付款, 5确认收款
     */
    public function update()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $para_name_array = array('id', 'status', 'to_id');
        $record = $this->get_para($para_name_array);
        $time = date("Y-m-d H:i:s");
        $message = $this->check_para_add($record);

        $response = array('status' => -1, 'message' => '');

        if (!$this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($message == null) {

            if (!$this->Record_m->is_exist($record['id'])) {
                $response['message'] = '该借卡记录不存在';
                echo json_encode($response);
                return;
            }

            if (!($record['status'] >= 0 && $record['status'] < 5)) {
                $response['message'] = '该借卡记录状态无效';
                echo json_encode($response);
                return;
            }

            if ($this->Record_m->update($record, $time)) {
                $response['status'] = 0;
                $response['message'] = 'success';
                $this->push_record($record, $time);
            }
        } else {
            $response['status'] = -1;
            $response['message'] = $message;
        }

        echo json_encode($response);
    }

    private function push_record($record, $time)
    {
        $row = $this->Record_m->get_by_id($record['id']);
        $status = $record['status'];
        $phone_type = $this->User_m->query_phone_type($record['to_id']);
        $user = $this->User_m->query_user($row['borrow_id']);
        $push = new Push_Util();

        if ($phone_type == 1) {
            $title = '';
            $content = '';
            switch ($status) {
//                case 0:
//                    $title = $row['borrow_name'];
//                    $content = '撤销了借卡请求';
//                    break;
                case 1:
                    $title = $row['lend_name'];
                    $content = '同意了您的借卡请求';
                    break;
                case 2:
                    $title = $row['lend_name'];
                    $content = '已经收到了您归还的卡';
                    break;
                case 3:
                    $title = $row['borrow_name'];
                    $content = '向你支付用卡费用';
                    break;
                case 4:
                    $title = $row['lend_name'];
                    $content = '已经收到了你的用卡费用';
            }
            if ($status == 3) {
                $push->push_android_record($row['lend_id'], $title, $content, $user['open_id'], $user['gender'], $user['avatar'], $time, $row['id']);
            } else {
                $push->push_android_record($row['borrow_id'], $title, $content, $user['open_id'], $user['gender'], $user['avatar'], $time, $row['id']);
            }
        } else {
            // iOS push
        }
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

    /**
     * 通过参数名称数组获取GET POST 的参数
     * @param $para_name_array
     * @return array
     */
    public function get_para($para_name_array)
    {
        $result = array();
        foreach ($para_name_array as $value) {
            if (isset($_GET[$value]))
                $result[$value] = $_GET[$value];
            else
                $result[$value] = null;
        }

        foreach ($para_name_array as $value) {
            if (isset($_POST[$value]))
                $result[$value] = $_POST[$value];
            else
                $result[$value] = null;
        }

        return $result;
    }

    /**
     * 判断获取的参数是否为空, 有空的则返回错误信息
     * @param $record
     * @return null|string
     */
    public function check_para_add($record)
    {
        $message = null;
        foreach ($record as $key => $value) {
            if ($value == null) {
                $message = $key . "不能为null";
            }
        }
        return $message;
    }

    /**
     * 检验获取用户借卡记录的时候borrow_id和lend_id不能同时为空
     * @param $paras
     * @return null|string
     */
    private function check_borrow_lend_id($paras)
    {
        $message = null;
        if ($paras['borrow_id'] == null && $paras['lend_id'] == null)
            $message = "borrow_id 和 lend_id 不能同时为 null";

        return $message;
    }

    private function check_para_is_exist($paras)
    {
        $message = null;
        if ($paras['card_id'] == null) {
            $message = "card_id 不能为null";
        } elseif ($paras['borrow_id'] == null) {
            $message = "borrow_id 不能为null";
        } elseif ($paras['lend_id'] == null) {
            $message = "lend_id 不能为null";
        }

        return $message;
    }
}