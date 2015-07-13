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