<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/22
 * Time: 11:44
 */
class Add_reject_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Record_m');
    }

    public function index()
    {

        Log_Util::log_param($_POST, __CLASS__);

        if ($this->User_m->verify_session_key($_GET)) {
            $record = $this->get_data();
            $message = $this->check_data($record);

            if ($message == null) {
                $this->Record_m->update($record);
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

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_data()
    {
        $record['id'] = array_key_exists("id", $_POST) ? $_POST["id"] : null;
        $record['status'] = array_key_exists("status", $_POST) ? $_POST["status"] : null;

        if ($record['status'] != null) {
            switch ($record['status']) {
                case 3:
                    $record['cancel_time'] = date("Y-m-d H:i:s");
                    break;
                case 2:
                    $record['apply_time'] = date("Y-m-d H:i:s");
                    break;
                case 1:
                    $record['return_time'] = date("Y-m-d H:i:s");
                    break;
                case -1:
                    $record['lend_time'] = date("Y-m-d H:i:s");
                    break;
                case -2:
                    $record['agree_time'] = date("Y-m-d H:i:s");
                    break;
                case -3:
                    $record['reject_time'] = date("Y-m-d H:i:s");
                    break;
            }
        }
    }

    public function check_data($record)
    {
        $message = null;
        if ($record['id'] == null) {
            $message = 'id 不能为null';
        } else if ($record['status'] == null) {
            $message = 'status 不能为null';
        }
        return $message;
    }
}