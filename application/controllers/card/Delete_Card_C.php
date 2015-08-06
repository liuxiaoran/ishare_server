<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:05
 */
class Delete_Card_C extends CI_Controller
{
    private $param_until;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Card_m');
        $this->param_until = new Param_Util();
    }
    /**
     * 每次点击一个客户时调用的接口，返回登录起接收的聊天
     * @param $card_ids 需要删除的卡id
     * @return array 消息的数组
     */
    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);
        $param_names = array('card_id');
        $param_need_names = array('open_id', 'key', 'card_id');
        $params = $this->param_until->get_param($param_names, $_GET);
        $message = $this->param_until->check_param($_GET, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_GET)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                if ($this->Card_m->delete_card($params['card_id'])) {
                    $ret = Ret_Factory::create_ret(0);
                } else {
                    $ret = Ret_Factory::create_ret(-2);
                }
            }
        }

        echo json_encode($ret);
    }

}