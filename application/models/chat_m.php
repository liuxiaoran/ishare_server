<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:27
 */
class Chat_m extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
    }

    public function add_chat($chat)
    {
        $id = 0;
        try {
            $this->load->database();
            $this->db->insert('chat', $chat);
            $id = $this->db->insert_id();
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $id;
    }

    public function update_status($id, $status)
    {
        $result = null;
        try {
            $this->load->database();
            $sql = 'UPDATE chat set status = ? WHERE id = ?';
            $result = $this->db->query($sql, array($status, $id));
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $result;
    }

    public function query($user, $chat, $time, $size)
    {
        $chats = array();
        try {
            $this->load->database();

            $sql = 'SELECT * FROM chat';
            $sql = $sql . ' WHERE ((from_user = ' . $user . ' AND to_user = ' . $chat .
                ') OR (from_user = ' . $chat . ' AND to_user = ' . $user . '))';
            if ($time != null) {
                $sql = $sql . " AND time < '" . $time . "'";
            }
            $sql = $sql . ' ORDER BY time DESC';
            $sql = $sql . ' LIMIT 0, ' . $size;
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
     * 客服获取未读消息接口
     * @param $user 客户id
     * @param $size 获取消息的数目
     * @param $customer_service 客服账户id，设定了默认值为0，调用时可以不用写最后一个参数
     * @return array 消息的数组
     */
    public function query_service($user, $size, $customer_service = '0')
    {
        $chats = array();
        try {
            $this->load->database();

            $sql = 'SELECT * FROM chat';
            $sql = $sql . ' WHERE ((from_user = ' . $user . ' AND to_user = ' . $customer_service .
                ') OR (from_user = ' . $customer_service . ' AND to_user = ' . $user . '))';

            /*status = 0 在数据库中代表未读*/
            $sql = $sql . ' AND status = 0';
            $sql = $sql . ' ORDER BY time DESC';
            $sql = $sql . ' LIMIT 0, ' . $size;
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
     * 客服获取历史消息接口
     * @param $user 客户id
     * @param $time 从某个时间点起往过去获取消息
     * @param $size 获取消息的数目
     * @param $customer_service 客服账户id，设定了默认值为0，调用时可以不用写最后一个参数
     * @return array 消息的数组
     */
    public function query_service_history($user, $time, $size, $customer_service = '0')
    {
        $chats = array();
        try {
            $this->load->database();

            $sql = 'SELECT * FROM chat';
            $sql = $sql . ' WHERE ((from_user = ' . $user . ' AND to_user = ' . $customer_service .
                ') OR (from_user = ' . $customer_service . ' AND to_user = ' . $user . '))';
            if ($time != null) {
                $sql = $sql . " AND time < '" . $time . "'";
            }
            $sql = $sql . ' ORDER BY time DESC';
            $sql = $sql . ' LIMIT 0, ' . $size;
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
    public function query_customer( $size, $customer_service = '0')
    {
        $customers = array();
        try {
            $this->load->database();

            $sql = 'SELECT DISTINCT u.nickname, u.avatar FROM users u, chat c';
            $sql = $sql . ' ( c.from_user = ' . $customer_service . ' AND c.from_user = u.open_id ) OR ( c.to_user = ' . $customer_service . ' AND c.from_user = u.open_id )';
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

    public function get_last_chat($order_id)
    {
        $result = null;
        try {
            $this->load->database();
            $sql = 'SELECT content FROM chat WHERE order_id = ' . $order_id . ' ORDER BY time DESC LIMIT 1';
            $query = $this->db->query($sql);

            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $result = $row['content'];
            }

            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $result;
    }
}