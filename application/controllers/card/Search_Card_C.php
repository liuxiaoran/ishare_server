<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/28
 * Time: 11:04
 */

class Search_Card_C extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->load->model("User_m");
        $this->load->model("Card_m");
    }

    public function index() {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_GET)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $param_names = array("keyword", "longitude", "latitude", "page_num", "page_size");
            $param = $this->get_para($param_names);
            $message = $this->check_para($param);

            if ($message != null) {
                $ret['status'] = -1;
                $ret['message'] = $message;
            } else {
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $this->Card_m->search($param['keyword'], $param['longitude'], $param['latitude'], $param['page_num'], $param['page_size']);
                ;
            }
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    private function get_para($para_names)
    {
        $result = array();

        foreach ($para_names as $para_name) {
            if (isset($_GET[$para_name])) {
                $result[$para_name] = $_GET[$para_name];
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