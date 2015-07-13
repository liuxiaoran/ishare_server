<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/11
 * Time: 16:08
 */
class Query_Card_I_Share extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_POST)) {
            $para_name_array = array('open_id', 'page_num', 'page_size');
            $data = $this->get_data($para_name_array);
            $message = $this->check_data($data);

            if ($message != null) {
                $ret['status'] = 0;
                $ret['message'] = 'success';
                $ret['data'] = $this->Card_m->query_i_share($data['open_id'], $data['page_num'], $data['page_size']);
            } else {
                $ret['status'] = -1;
                $ret['message'] = $message;
            }
        } else {
            $ret['status'] = -1;
            $ret['message'] = 'not login';
        }

        echo json_encode($ret);
    }

    public function get_data($para_name_array)
    {
        $data = array();

        foreach ($para_name_array as $para_name) {
            $data[$para_name] = $this->input->post($para_name);
            if ($data[$para_name] === false) {
                $data[$para_name] = null;
            }
        }

        return $data;
    }

    public function check_data($data)
    {
        $message = null;
        if ($data['open_id'] == null) {
            $message = 'open_id不能为空';
        } else if ($data['page_num']) {
            $message = 'page_num不能为空';
        } else if ($data['page_size']) {
            $message = 'page_size不能为空';
        }

        return $message;
    }
}