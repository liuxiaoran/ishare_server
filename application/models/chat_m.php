<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:27
 */
class Chat_m extends CI_Model
{
    private $dao;
    private $param_util;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new Base_Dao();
        $this->param_util = new Param_Util();
    }

    public function add_chat($chat)
    {
        $table_name = 'chat';
        $chat_names = array('from_user', 'to_user', 'type', 'content', 'card_id', 'card_type', 'time');
        $param = $this->param_util->get_param($chat_names, $chat);
        return $this->dao->insert($table_name, $param);
    }

    public function update_status($id, $status)
    {
        $table_name = 'chat';
        $param['status'] = $status;
        $where['id'] = $id;
        return $this->dao->update($table_name, $param, $where);
    }

    public function query($order_id, $time, $size)
    {
        $sql = "SELECT * FROM chat WHERE order_id = ? AND time < ? ORDER BY time DESC LIMIT 0,?";
        $param = array((int) $order_id, $time, (int) $size);
        return $this->dao->query_by_sql($sql, $param);
    }

    public function get_last_chat($order_id)
    {
        $sql = 'SELECT content FROM chat WHERE order_id = ? ORDER BY time DESC LIMIT 1';
        $param = array((int) $order_id);
        $result =  $this->dao->query_by_sql($sql, $param);
        return count($result) == 0? null : $result[0];
    }
}