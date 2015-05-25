<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 15:44
 */
class Query_Card_By_Phone_C extends CI_Controller
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
        if ($this->User_m->verify_session_key($_GET)) {
            $phone = array_key_exists('$phone', $_GET) ? $_GET['phone'] : null;
            $page_num = array_key_exists("page_num", $_GET) ? $_GET["page_num"] : 1;
            $page_size = array_key_exists("page_size", $_GET) ? $_GET["page_size"] : 10;

            $data = $this->Card_m->query_by_phone($phone, $page_num, $page_size);
            if (count($data) != 0) {
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $data;
            } else {
                $ret['status'] = -1;
                $ret['message'] = 'exe sql failure';
                $ret['data'] = null;
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
            $ret['data'] = null;
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

}