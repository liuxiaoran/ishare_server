<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/25
 * Time: 15:16
 */

class Update_Record_C extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Record_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);
        $response = array();
        if ($this->User_m->verify_session_key($_POST)) {
            $para_name_array = array('id', 'status', 'to_id');
            $param = $this->get_para($para_name_array);
            $message = $this->check_param($param);

            if($message == null) {
                $record = $this->Record_m->query_by_id($param['id']);
                $time = date("Y-m-d H:i:s");
                if($record != null && $this->Record_m->update($param['id'], $record['card_id'], $record['type'], $param['status'], $time)) {
                    $response['status'] = 0;
                    $response['message'] = 'success';
                } else {
                    $response['status'] = -1;
                    $response['message'] = '服务器端数据错误！';
                }
            } else {
                $response['status'] = -1;
                $response['message'] = $message;
            }
        } else {
            $response['status'] = 2;
            $response['message'] = 'not login';
        }

        echo json_encode($response);
    }

    public function get_para($para_name_array)
    {
        $result = array();

        foreach ($para_name_array as $value) {
            if (isset($_POST[$value]))
                $result[$value] = $_POST[$value];
            else
                $result[$value] = null;
        }

        return $result;
    }

    public function check_param($paras)
    {
        $message = null;
        foreach ($paras as $key => $value) {
            if ($value == null) {
                $message = $key . f;
            }
        }
        return $message;
    }
}