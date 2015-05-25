<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/11
 * Time: 10:40
 */
class User_m extends CI_Model
{

    public function login($data)
    {
        $result = null;
        try {
            $this->load->database();
            $sql = 'select * from users where phone=? and pw = ?';
            $query = $this->db->query($sql, $data);
            $result = $query->row_array();
            $this->db->close();
        } catch (Exception $e) {
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $result;
    }

    public function get_session_key($open_id)
    {
        $key = md5($open_id + time());
        try {
            $sql = 'update users set session_key = ? where phone = ?';
            $this->db->query($sql, array($key, $open_id));
        } catch (Exception $e) {
            $key = null;
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }
        return $key;
    }

    public function is_register($phone)
    {
        try {
            $this->load->database();
            $sql = 'select * from users where phone = ' . $phone;
            Log_Util::log_sql($sql, __CLASS__);
            $query = $this->db->query($sql);
            $this->db->close();
        } catch (Exception $e) {
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        if ($query->num_rows() === 0) {
            return false;
        } else {
            return true;
        }
    }

    public function add_user($user)
    {
        $status = true;
        try {
            $this->db->insert('users', $user);
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function update_user_info($user)
    {
        $status = true;
        try {
            $sql = "UPDATE users SET phone = " . $user['phone'];
            if ($user['phone'] != null) {
                $sql = $sql . ", phone = '" . $user['phone'] . "'";
            }
            if ($user['nickname'] != null) {
                $sql = $sql . ", nickname = '" . $user['nickname'] . "'";
            }
            if ($user['avatar'] != null) {
                $sql = $sql . ", avatar = '" . $user['avatar'] . "'";
            }
            if ($user['gender'] != null) {
                $sql = $sql . ", gender = '" . $user['gender'] . "'";
            }
            $sql = $sql . " WHERE phone = '" . $user['phone'] . "'";

            $this->load->database();
            $this->db->query($sql);
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function update_user_phone($open_id, $device_token = 0, $phone_type = 0)
    {
        $result = true;
        try {
            $sql = 'UPDATE user SET open_id = ' . $open_id;
            if ($device_token != 0) {
                $sql = $sql . ', device_token = ' . $device_token;
            }
            if ($phone_type != 0) {
                $sql = $sql . ', phone_type = ' . $phone_type;
            }
            $sql = $sql . ' WHERE phone = ' . $open_id;
            $this->load->database();
            $this->db->query($sql);
            $this->db->close();
        } catch (Exception $e) {
            $result = false;
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function query_user_phone($open_id)
    {
        $row = array();
        try {
            $sql = 'SELECT phone_type, device_token FROM users WHERE phone = ' . $open_id;
            Log_Util::log_sql($sql, __CLASS__);
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();
            $row = $query->row_array();
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $row;
    }

    public function verify_session_key($data)
    {
        $open_id = array_key_exists("phone", $data) ? $data["phone"] : null;
        $key = array_key_exists("key", $data) ? $data["key"] : null;

        try {
            $this->load->database();
            $sql = "select open_id from users where phone = ? and session_key= ?";
            $result = $this->db->query($sql, array($open_id, $key));
            $this->db->close();
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        if ($result->num_rows() === 1) {
            return true;
        } else {
            return false;
        }
    }

    public function query_user($open_id)
    {
        $row = array();
        try {
            $sql = 'SELECT * FROM users WHERE phone = ' . $open_id;
            Log_Util::log_sql($sql, __CLASS__);
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();
            $row = $query->row_array();
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $row;
    }

}