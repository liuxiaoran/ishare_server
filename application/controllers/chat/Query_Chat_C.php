<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/20
 * Time: 10:23
 */
class Query_Chat_C extends CI_Controller
{

    public function __Construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Chat_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_POST)) {
            $param_names = array("order_id", "time", "size");
            $params = $this->get_para($param_names);
            $message = $this->check_para($params);

            if ($message === null) {
                $ret['data'] = $this->Chat_m->query($params['order_id'], $params['time'], $params['size']);
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