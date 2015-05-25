<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/23
 * Time: 16:44
 */
class Query_User_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if ($this->verify()) {
            $phone = array_key_exists("phone", $_GET) ? $_GET["phone"] : null;
            $user = $this->User_m->query_user($phone);

            $ret['status'] = 0;
            $ret['message'] = 'success';
            $ret['data'] = $user;
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
            $ret['data'] = null;
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function verify()
    {
        $phone = array_key_exists("phone", $_GET) ? $_GET["phone"] : null;//$_GET["phone"];
        $key = array_key_exists("key", $_GET) ? $_GET["key"] : null;//$_GET["key"];

        return $this->User_m->verify_session_key($phone, $key);
    }
}