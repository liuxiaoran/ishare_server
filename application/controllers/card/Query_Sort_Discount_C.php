<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:22
 */

class Query_Sort_Discount_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
    }

    public function index() {
        Log_Util::log_param($_POST,  __CLASS__);

        $ret = array();
        if (!$this->verify()) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $keyword = array_key_exists("keyword", $_POST) ? $_POST["keyword"] : null;
            $trade_type = array_key_exists("trade_type", $_POST) ? $_POST["trade_type"] : null;
            $page_num = array_key_exists("page_num", $_POST) ? $_POST["page_num"] : 1;
            $page_size = array_key_exists("page_size", $_POST) ? $_POST["page_size"] : 10;

            $data = $this->Card_m->query_sort_discount($keyword, $trade_type, $page_num, $page_size);
            $ret['status'] = 0;
            $ret['message'] = 'success';
            $ret['data'] = $data;
        }

        Log_Util::log_info($ret['message'], __CLASS__);

        echo json_encode($ret);
    }

    public function verify() {
        $phone = array_key_exists("phone", $_POST) ? $_POST["phone"] : null;
        $key = array_key_exists("key", $_POST) ? $_POST["key"] : null;

        return $this->User_m->verify_session_key($phone, $key);

    }
}