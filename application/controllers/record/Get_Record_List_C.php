<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/26
 * Time: 16:15
 */
class Get_Record_List_C extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Record_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_POST)) {
            $para_name_array = array('open_id', 'longitude', 'latitude', 'page_size', 'page_num');
            $paras = $this->get_para($para_name_array);
            $message = $this->check_para($paras);

            if ($message == null) {
                $ret['data'] = $this->Record_m->get_order($paras['open_id'], $paras['longitude'], $paras['latitude'], $paras['page_size'], $paras['page_num']);
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

        Log_Util::log_info($ret['data'], __CLASS__);

        echo json_encode($ret);
    }

    public function get_para($para_name_array)
    {
        $result = array();

        foreach ($para_name_array as $value) {
            if (isset($_POST[$value]))
                $result[$value] = $_POST[$value];
            else
                $result[$value] = null;
        }

        return $result;
    }

    public function check_para($paras)
    {
        $message = null;
        foreach ($paras as $key => $value) {
            if ($value == null) {
                $message = $key . '不能为空';
            }
        }

        return $message;
    }

}