<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:22
 */
class Query_Card_Sort_Distance_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if (!$this->verify()) {
            $ret = array('status' => '2', 'message' => "not login");
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $keyword = array_key_exists("keyword", $_GET) ? $_GET["keyword"] : null;
            $trade_type = array_key_exists("trade_type", $_GET) ? $_GET["trade_type"] : -1;
            $longitude = array_key_exists("longitude", $_GET) ? $_GET["longitude"] : 0;
            $latitude = array_key_exists("latitude", $_GET) ? $_GET["latitude"] : 0;
            $pageNum = array_key_exists("pageNum", $_GET) ? $_GET["page_num"] : 1;
            $pageSize = array_key_exists("pageSize", $_GET) ? $_GET["page_size"] : 10;

            $data = $this->Card_m->query_sort_distance($keyword, $trade_type, $longitude, $latitude, $pageNum, $pageSize);
            if (count($data) != 0) {
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $data;
            } else {
                $ret['status'] = -1;
                $ret['message'] = 'exe sql failure';
                $ret['data'] = null;
            }
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function verify()
    {
        $phone = array_key_exists("phone", $_GET) ? $_GET["phone"] : null;
        $key = array_key_exists("key", $_GET) ? $_GET["key"] : null;

        return $this->User_m->verify_session_key($phone, $key);
    }

}