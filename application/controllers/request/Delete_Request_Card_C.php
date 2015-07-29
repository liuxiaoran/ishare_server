<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
/**
 * Created by Intellij.
 * User: 源初
 * Date: 2015/7/25
 * Time: 20:31
 */

class Delete_Request_Card_C extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Request_card_m');
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);
        $paras = $this->get_para(array('id'));

        $response = array('status' => -1, 'message' => '');

        if ( ! $this->is_login()) {
            $this->not_login_response();
            return;
        }

        if ($paras['id'] != null) {
            if ($this->Request_card_m->delete($paras['id'])) {
                $response['status'] = 0;
                $response['message'] = 'success';
                echo json_encode($response);
                return;
            } else {
                $response['message'] = 'delete failed';
                echo json_encode($response);
                return;
            }
        }

        $response['message'] = 'id不能为空';
        echo json_encode($response);
    }

    private function get_para($para_name_array)
    {
        $result = array();

        foreach($para_name_array as $value) {
            $result[$value] = $this->input->post($value);
            if ($result[$value] === false) $result[$value] = null;
        }

        return $result;
    }

    private function is_login()
    {
        $paras = $this->get_para(array('open_id', 'key'));

        return $this->User_m->verify_session_key($paras);
    }

    private function not_login_response()
    {
        $response['status'] = 2;
        $response['message'] = 'not login';
        echo json_encode($response);
    }
}