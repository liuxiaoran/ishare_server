<?php
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/17
 * Time: 17:31
 */

class Get_My_Request_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Request_card_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_POST)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $open_id = $_POST['open_id'];
            $ret['data'] = $this->Request_card_m->get_my_request($open_id);
            $ret['status'] = 0;
            $ret['message'] = 'success';
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }
}