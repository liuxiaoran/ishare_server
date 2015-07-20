<?php
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/16
 * Time: 11:56
 */

class customer_service_m extends CI_Model {

    public function add_chat($chat) {
        $id = 0;
        try {
            $this->load->database();
            $this->db->insert('customer_service', $chat);
            $id = $this->db->insert_id();
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $id;
    }

    public function get_chat($user, $time, $size)
    {
        $chats = array();
        try {
            $this->load->database();

            $sql = 'SELECT * FROM customer_service';
            $sql = $sql . " WHERE ((from_user = '" . $user . "') OR (to_user = '" . $user . "'))";
            if ($time != null) {
                $sql = $sql . " AND time < '" . $time . "'";
            }
            $sql = $sql . " ORDER BY time DESC";
            $sql = $sql . " LIMIT 0, " . $size;
            $query = $this->db->query($sql);
            Log_Util::log_sql($sql, __CLASS__);

            foreach ($query->result_array() as $chat) {
                array_push($chats, $chat);
            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $chats;
    }

    /**
     * 客服获取客户姓名和头像接口
     * @param $size 获取客户的数目
     * @param $customer_service 客服账户id，设定了默认值为0，调用时可以不用写最后一个参数
     * @return array 消息的数组
     */
    public function get_avatar( $size, $server_openid )
    {
        $customers = array();
        try {
            $this->load->database();

            $sql = 'SELECT DISTINCT u.nickname, u.avatar FROM users u, customer_service c WHERE';
            $sql = $sql . " c.status = 1 AND ( c.to_user = '" . $server_openid . "' AND c.from_user = u.open_id )";
            $sql = $sql . ' ORDER BY time DESC';
            $sql = $sql . ' LIMIT 0, ' . $size;
            $query = $this->db->query($sql);
            Log_Util::log_sql($sql, __CLASS__);

            foreach ($query->result_array() as $customer) {
                array_push($customers, $customer);
            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $customers;
    }
}