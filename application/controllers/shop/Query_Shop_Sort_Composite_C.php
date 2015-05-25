<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/18
 * Time: 14:53
 */
class Query_Shop_Sort_Composite_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Shop_m');
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if (!$this->verify()) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $keyword = array_key_exists("keyword", $_GET) ? $_GET["keyword"] : null;
            $longitude = array_key_exists("longitude", $_GET) ? $_GET["longitude"] : 0;
            $latitude = array_key_exists("latitude", $_GET) ? $_GET["latitude"] : 0;
            $page_num = array_key_exists("page_num", $_GET) ? $_GET["page_num"] : 1;
            $page_size = array_key_exists("page_size", $_GET) ? $_GET["page_size"] : 10;

            $message = $this->check_data($longitude, $latitude);

            if ($message != null) {
                $ret['status'] = -1;
                $ret['message'] = $message;
                $ret['data'] = null;
            } else {
                $data = $this->Shop_m->query_sort_composite($keyword, $longitude, $latitude, $page_num, $page_size);
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $data;
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

    public function check_data($longitude, $latitude)
    {
        $msg = null;
        if ($longitude == null) {
            $msg = '经度不能为空';
        } else if ($latitude == null) {
            $msg = '纬度不能为空';
        }
        return $msg;
    }
}