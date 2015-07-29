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
    /**
     * 每次点击一个客户时调用的接口，返回登录起接收的聊天
     * @param $card_ids 需要删除的卡id
     * @return array 消息的数组
     */
    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_GET)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $card_id = $_GET['card_id'];
            if ($card_id == null) {
                $ret['status'] = -1;
                $ret['message'] = 'card_id is null';
            } else {
                if ($this->Card_m->delete_card($card_id)) {
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

}