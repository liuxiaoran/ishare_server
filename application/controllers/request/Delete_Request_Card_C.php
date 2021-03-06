<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by Intellij.
 * User: 源初
 * Date: 2015/7/25
 * Time: 20:31
 */
class Delete_Request_Card_C extends CI_Controller
{
    private $param_until;

    public function __construct() {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Request_card_m');
        $this->param_until = new Param_Util();
    }

    public function index() {
        Log_Util::log_param($_POST, __CLASS__);

        $param_names = array('id');
        $param_need_names = array('open_id', 'key', 'id');
        $params = $this->param_until->get_param($param_names, $_POST);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                if ($this->Request_card_m->delete($params['id'])) {
                    $ret = Ret_Factory::create_ret(0);
                    return;
                } else {
                    $ret = Ret_Factory::create_ret(-2);
                }
            }
        }

        echo json_encode($ret);
    }

}