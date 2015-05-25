<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:05
 */
class Delete_Card_C extends CI_Controller
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
        if (!$this->verify()) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $card_ids = $this->get_card_id();
            if ($card_ids == null) {
                $ret['status'] = -1;
                $ret['message'] = 'card_ids is null';
            } else {
                if ($this->Card_m->deleteCard($card_ids)) {
                    $ret['status'] = 0;
                    $ret['message'] = 'success';
                } else {
                    $ret['status'] = -1;
                    $ret['message'] = 'exe sql error';
                }
            }
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function get_card_id()
    {
        if (array_key_exists("card_ids", $_POST)) {
            return json_decode($_POST("card_ids"));
        } else {
            return null;
        }
    }

    public function verify()
    {
        $phone = array_key_exists("phone", $_POST) ? $_POST["phone"] : null;
        $key = array_key_exists("key", $_POST) ? $_POST["key"] : null;

        return $this->User_m->verify_session_key($phone, $key);
    }
}