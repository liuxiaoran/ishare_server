<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/17
 * Time: 17:31
 */

class Get_My_Request_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Request_card_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_POST)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $para_names = array('open_id', 'page_num', 'page_size');
            $paras = $this->get_para($para_names);
            $ret['data'] = $this->Request_card_m->get_my_request($paras['open_id'], $paras['page_num'], $paras['page_size']);
            $ret['status'] = 0;
            $ret['message'] = 'success';
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
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