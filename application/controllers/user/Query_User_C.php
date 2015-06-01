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
        if ($this->User_m->verify_session_key($_GET)) {
            $open_id = array_key_exists("open_id", $_GET) ? $_GET["open_id"] : null;
            $user = $this->User_m->query_user($open_id);

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

}