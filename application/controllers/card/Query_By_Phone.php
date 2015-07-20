<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 15:44
 */

class Query_By_Phone extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
    }

    public function index() {
        Log_Util::log_param($_POST,  __CLASS__);

        $ret = array();
        if($this->verify()) {
            $phone = array_key_exists('$phone', $_GET) ? $_GET['phone'] : null;
            $page_num = array_key_exists("pageNum", $_GET) ? $_GET["pageNum"] : 1;
            $page_size = array_key_exists("pageSize", $_GET) ? $_GET["pageSize"] : 10;

            $data = $this->Card_m->query_by_phone($phone, $page_num, $page_size);
            if (count($data) != 0) {
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $data;
            } else {
                $ret['status'] = -1;
                $ret['message'] = 'exe sql failure';
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        }

        Log_Util::log_info($ret['message'], __CLASS__);

        echo json_encode($ret);
    }

    public function verify() {
        $phone = array_key_exists("phone", $_GET) ? $_GET["phone"] : null;//$_GET["phone"];
        $key = array_key_exists("key", $_GET) ? $_GET["key"] : null;//$_GET["key"];

        if ($this->User_m->verifyKey($phone, $key)) {
            return true;
        } else {
            return false;
        }
    }
}