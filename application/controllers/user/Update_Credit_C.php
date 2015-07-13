<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/4
 * Time: 19:45
 */
class Update_Credit_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_POST)) {
            $para_name_array = array('open_id', 'real_name', 'per_photo', 'ID', 'work_unit', 'work_card');
            $para = $this->get_para($para_name_array);
            if ($this->User_m->update_credit($para)) {
                $ret['status'] = 0;
                $ret['message'] = 'success';
            } else {
                $ret['status'] = -1;
                $ret['message'] = 'failure';
            }
        } else {
            $ret['status'] = -2;
            $ret['message'] = 'not login';
        }

        echo json_encode($ret);
    }

    private function get_para($para_name_array)
    {
        $result = array();

        foreach ($para_name_array as $para_name) {
            if (isset($_POST[$para_name])) {
                $result[$para_name] = $_POST[$para_name];
            } else {
                $result[$para_name] = null;
            }
        }

        return $result;
    }

}