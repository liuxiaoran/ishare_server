<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/18
 * Time: 10:39
 */
class Record_With_Card_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Record_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        if ($this->User_m->verify_session_key($_POST)) {
            $paras = $this->get_para(array('card_id', 'borrow_id', 'lend_id'));
            $paras['type'] = 1;
            $error_message = $this->check_para_is_exist($paras);

            if ($error_message == null) {
                if ($data = $this->Record_m->is_exist_by_three_id($paras['card_id'], $paras['type'], $paras['borrow_id'], $paras['lend_id'])) {
                    $response['status'] = 0;
                    $response['message'] = 'success';
                    $response['data'] = $data;
                } else {
                    $response['status'] = 1;
                    $response['message'] = 'success';
                    $response['data'] = $this->Record_m->query_card_with_three_id($paras['card_id'], $paras['borrow_id'], $paras['lend_id']);
                }
            } else {
                $response['status'] = -1;
                $response['message'] = 'not login';
            }

            echo json_encode($response);
        }
    }

    public function get_para($paras_name_array)
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

    public function check_para_is_exist($paras)
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