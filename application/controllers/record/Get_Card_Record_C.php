<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/18
 * Time: 10:39
 */
class Get_Card_Record_C extends CI_Controller
{
    private $param_until;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Record_m');
        $this->load->model('Card_m');
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);
        $param_names = array('card_id', 'borrow_id', 'lend_id');
        $param_need_names = array('open_id', 'key', 'card_id', 'borrow_id', 'lend_id');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data = $this->Record_m->query_card_record($params['card_id'], $params['borrow_id'], $params['lend_id']);
                if ($data != null) {
                    $ret = Ret_Factory::create_ret(0, null, $data);
                } else {
                    $message = 'no record';
                    $data = $this->Card_m->query($params['card_id'], $params['borrow_id'], $params['lend_id']);
                    $ret = Ret_Factory::create_ret(-3, $message, $data);
                }
            }
        }

        echo json_encode($ret);
    }
}