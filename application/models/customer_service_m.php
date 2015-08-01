<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/16
 * Time: 11:56
 */

class customer_service_m extends CI_Model {
    private $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new Base_Dao();
    }

    public function add_chat($chat) {
        $table_name = 'customer_service';
        return $this->dao->insert($table_name, $chat);
    }

    public function get_chat($user, $time, $size)
    {
        $sql = "SELECT * FROM customer_service WHERE ((from_user = ?) OR (to_user = ?))"
            . " AND time < ? ORDER BY time DESC LIMIT 0, ?";
        $param = array($user, $user, $time, (int) $size);
        return $this->dao->query_by_sql($sql, $param);
    }

    /**
     * 客服获取客户姓名和头像接口
     * @param $size 获取客户的数目
     * @param $customer_service 客服账户id，设定了默认值为0，调用时可以不用写最后一个参数
     * @return array 消息的数组
     */
    public function get_avatar($size, $server_openid)
    {
        $sql = 'SELECT DISTINCT u.nickname, u.avatar, u.open_id FROM users u, customer_service c WHERE'
            . ' c.status = 0 AND ( c.to_user = ? AND c.from_user = u.open_id) ORDER BY time DESC'
            . ' LIMIT 0, ?';
        $param = array($server_openid,(int) $size);
        return $this->dao->query_by_sql($sql, $param);
    }
}