<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/22
 * Time: 10:26
 */
class Add_Borrow_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Record_m');
        $this->load->model('Card_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->verify()) {
            $record = $this->get_data();
            $message = $this->check_data($record);
            if ($message == null) {
                $this->Record_m->add($record);
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

    public function verify()
    {
        $phone = array_key_exists("phone", $_POST) ? $_POST["phone"] : null;
        $key = array_key_exists("key", $_POST) ? $_POST["key"] : null;

        return $this->User_m->verify_session_key($phone, $key);
    }

    public function get_data()
    {
        $record['open_id'] = array_key_exists("open_id", $_POST) ? $_POST["open_id"] : null;
        $record['card_id'] = array_key_exists("card_id", $_POST) ? $_POST["card_id"] : null;
        $record['status'] = 2;//3=取消申请，2=申请借卡,1=归还,0=还款-1=借出，-2=同意借卡，-3=拒绝借卡
        $record['apply_time'] = date("Y-m-d H:i:s");
    }

    public function check_data($record)
    {
        $message = null;
        if ($record['open_id'] == null) {
            $message = 'open_id 不能为null';
        } else if ($record['card_id'] == null) {
            $message = 'card_id 不能为null';
        }
        return $message;
    }
}