<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/21
 * Time: 18:44
 */
class Query_Location_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Owner_location');
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if ($this->verify()) {
            $item_id = array_key_exists("item_id", $_GET) ? $_GET["item_id"] : null;
            if ($item_id != null) {
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $this->Owner_location->get_owner_location($item_id);
            } else {
                $ret['status'] = -1;
                $ret['message'] = 'item_id不能为空';
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
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