<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/23
 * Time: 16:07
 */
class Get_Collection_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Collection_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_POST)) {
            $para_name_array = array('open_id', 'page_num', 'page_size');
            $para = $this->get_para($para_name_array);
            $message = $this->check_para($para);
            if ($message == null) {
                $ret['data'] = $this->Collection_m->get($para['open_id'], $para['page_num'], $para['page_size']);
                $ret['status'] = 0;
                $ret['message'] = 'success';
            } else {
                $ret['status'] = -1;
                $ret['message'] = 'failure';
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        }

        echo json_encode($ret);
    }

    private function get_para($para_name_array)
    {
        $result = array();

        foreach ($para_name_array as $para_name) {
            if (isset($_POST[$para_name])) {
                $result[$para_name] = $_POST[$para_name];
            } else {
                $result[$para_name] = null;
            }
        }

        return $result;
    }

    public function check_para($para)
    {
        $message = null;
        foreach ($para as $key => $value) {
            if ($value == null) {
                $message = $key . '不能为空';
            }
        }
        return $message;
    }
}