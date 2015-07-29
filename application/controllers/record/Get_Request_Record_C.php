<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/18
 * Time: 10:39
 */
class Get_Request_Record_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Record_m');
        $this->load->model('Request_card_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        if ($this->User_m->verify_session_key($_POST)) {
            $paras = $this->get_param(array('request_id', 'borrow_id', 'lend_id'));
            $error_message = $this->check_param($paras);

            if ($error_message == null) {
                $data = $this->Record_m->query_request_record($paras['request_id'], $paras['borrow_id'], $paras['lend_id']);
                if ($data != null) {
                    $response['status'] = 0;
                    $response['message'] = 'success';
                    $response['data'] = $data;
                } else {
                    $response['status'] = -1;
                    $response['message'] = 'failure';
                    $response['data'] = $this->Request_card_m->query($paras['request_id'], $paras['borrow_id'], $paras['lend_id']);
                }
            }
        } else {
            $response['status'] = -1;
            $response['message'] = 'not login';
        }

        echo json_encode($response);
    }

    public function get_param($paras_name_array)
    {
        $result = array();
        foreach ($paras_name_array as $para_name) {
            if (isset($_POST[$para_name])) {
                $result[$para_name] = $_POST[$para_name];
            } else {
                $result[$para_name] = null;
            }
        }
        return $result;
    }

    public function check_param($paras)
    {
        $message = null;
        foreach ($paras as $key => $value) {
            if ($value == null) {
                $message = $key . '不能为空';
            }
            break;
        }
        return $message;
    }
}