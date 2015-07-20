<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/22
 * Time: 10:26
 */
class Add_Record_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Record_m');
        $this->load->model('User_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_POST)) {
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

    public function get_data()
    {
        $record['borrow_id'] = array_key_exists("borrow_id", $_POST) ? $_POST["borrow_id"] : null;
        $record['lend_id'] = array_key_exists("lend_id", $_POST) ? $_POST["lend_id"] : null;
        $record['card_id'] = array_key_exists("card_id", $_POST) ? $_POST["card_id"] : null;
        $record['status'] = 1;
        //1=申请借卡，2=取消申请，3=未归还（确认拿卡），4=借卡人还卡，5=借卡人还款,
        //-1=同意借卡，-2=拒绝借卡，-3=卡主借出卡，-4=未付款（确认还卡），-5=卡主确认收款
        //0=意外结束交易
        $record['t_apply'] = date("Y-m-d H:i:s");
        return $record;
    }

    public function check_data($record)
    {
        $message = null;
        if ($record['borrow_id'] == null) {
            $message = 'borrow_id 不能为null';
        } else if ($record['card_id'] == null) {
            $message = 'card_id 不能为null';
        }
        return $message;
    }
}