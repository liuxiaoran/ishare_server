<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/18
 * Time: 18:01
 */
class Query_Card_Sort_Composite_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
    }
    /**
     * 综合排序
     * @param $trade_type 卡的行业类型
     * @param $longitude 经纬度
     * @param $latitude
     * @param $page_num 分页
     * @param $page_size
     * @return array 消息的数组
     */
    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_GET)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
            $ret['data'] = null;
        } else {
            $trade_type = array_key_exists("trade_type", $_GET) ? $_GET["trade_type"] : null;
            $longitude = array_key_exists("longitude", $_GET) ? $_GET["longitude"] : null;
            $latitude = array_key_exists("latitude", $_GET) ? $_GET["latitude"] : null;
            $page_num = array_key_exists("page_num", $_GET) ? $_GET["page_num"] : 1;
            $page_size = array_key_exists("page_size", $_GET) ? $_GET["page_size"] : 10;

            $message = $this->check_data($longitude, $latitude);

            if ($message != null) {
                $ret['status'] = -1;
                $ret['message'] = $message;
                $ret['data'] = null;
            } else {
                $data = $this->Card_m->query_sort_composite($trade_type, $longitude, $latitude, $page_num, $page_size);
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $data;
            }
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
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