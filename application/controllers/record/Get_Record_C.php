<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/24
 * Time: 17:01
 */

class Get_Record_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Record_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);

        if ($this->User_m->verify_session_key($_POST)) {
            $paras = $this->get_para(array('id'));
            $message = $this->check_para($paras);
            if ($message == null) {
                $response['status'] = 0;
                $response['message'] = 'success';
                $response['data'] = $this->Record_m->query_order_by_id($paras['id']);
            } else {
                $response['status'] = -1;
                $response['message'] = 'id不能为空';
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

    public function check_para($paras)
    {
        $message = null;
        foreach ($paras as $key => $value) {
            if ($value == null) {
                $message = $key . '不能为空';
            }
        }

        return $message;
    }
}