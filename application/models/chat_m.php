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
            $this->db->insert('chat', $chat);
            $id = $this->db->insert_id();
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $id;
    }

    public function update_status($id, $status)
    {
        $result = null;
        try {
            $sql = 'UPDATE chat set status = ? WHERE id = ?';
            $result = $this->db->query($sql, array($status, $id));
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $result;
    }

    public function query($user, $chat, $time, $size)
    {
        $chats = array();
        try {
            $sql = 'SELECT * FROM chat';
            $sql = $sql . ' WHERE ((from_phone = ' . $user . ' AND to_phone = ' . $chat .
                ') OR (from_phone = ' . $chat . ' AND to_phone = ' . $user . '))';
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
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $chats;
    }
}