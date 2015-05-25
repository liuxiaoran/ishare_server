<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/22
 * Time: 14:14
 */
class Query_Record_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Record_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_GET)) {
            $record = $this->get_data();
            $message = $this->check_data($record);

            if ($message == null) {
                $ret['data'] = $this->Record_m->query_records($record['borrow_id'], $record['lend_id'], $record['type']);
                $ret['status'] = 0;
                $ret['message'] = 'success';
            } else {
                $ret['status'] = -1;
                $ret['message'] = $message;
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        }

        echo json_encode($ret);
    }

    public function get_data()
    {
        $record['borrow_id'] = array_key_exists("borrow_id", $_POST) ? $_POST["borrow_id"] : null;
        $record['lend_id'] = array_key_exists("lend_id", $_POST) ? $_POST["status"] : null;
        $record['type'] = array_key_exists("type", $_POST) ? $_POST["type"] : 0;
    }

    public function check_data($record)
    {
        $message = null;
        if ($record['borrow_id'] == null && $record['lend_id'] == null) {
            $message = 'borrow_id, lend_id不能同时为空';
        }
    }
}