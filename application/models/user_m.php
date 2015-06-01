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

    public function login($open_id)
    {
        $result = null;
        try {
            $this->load->database();
            $sql = "select phone, nickname, avatar, gender from users where open_id='" . $open_id . "'";
            Log_Util::log_sql($sql, __CLASS__);
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->row_array();
            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $result;
    }

    public function get_session_key($open_id)
    {
        $key = md5($open_id . time());
        try {
            $this->load->database();
            $sql = 'update users set session_key = ? where open_id = ?';
            $this->db->query($sql, array($key, $open_id));
            $this->db->close();
        } catch (Exception $e) {
            $key = null;
            $this->db->close();
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
            $this->db->close();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $query->num_rows() === 0 ? false : true;
    }

    public function add_user($user)
    {
        $status = true;
        try {
            $this->load->database();
            $this->db->insert('users', $user);
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            $this->db->close();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function update_user_info($user)
    {
        $status = true;
        try {
            $this->load->database();

            $sql = "UPDATE users SET open_id = '" . $user['open_id'] . "'";
            if (isset($user['phone_type'])) {
                $sql = $sql . ", phone_type = '" . $user['phone_type'] . "'";
            }
            if (isset($user['phone'])) {
                $sql = $sql . ", phone = '" . $user['phone'] . "'";
            }
            if (isset($user['nickname'])) {
                $sql = $sql . ", nickname = '" . $user['nickname'] . "'";
            }
            if (isset($user['avatar'])) {
                $sql = $sql . ", avatar = '" . $user['avatar'] . "'";
            }
            if (isset($user['gender'])) {
                $sql = $sql . ", gender = '" . $user['gender'] . "'";
            }
            $sql = $sql . " WHERE open_id = '" . $user['open_id'] . "'";

            Log_Util::log_sql($sql, __CLASS__);

            $this->db->query($sql);
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            $status = false;
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function update_user_phone($open_id, $device_token = 0, $phone_type = 0)
    {
        $result = true;
        try {
            $this->load->database();

            $sql = 'UPDATE user SET open_id = ' . $open_id;
            if ($device_token != 0) {
                $sql = $sql . ', device_token = ' . $device_token;
            }
            if ($phone_type != 0) {
                $sql = $sql . ', phone_type = ' . $phone_type;
            }
            $sql = $sql . ' WHERE phone = ' . $open_id;

            $this->db->query($sql);
            $this->db->close();
        } catch (Exception $e) {
            $result = false;
            $this->db->close();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function query_phone_type($open_id)
    {
        $row = array();
        try {
            $this->load->database();

            $sql = "SELECT phone_type FROM users WHERE open_id = '" . $open_id . "'";
            Log_Util::log_sql($sql, __CLASS__);

            $query = $this->db->query($sql);
            $row = $query->row_array();
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $row['phone_type'];
    }

    public function query_nickname($open_id)
    {
        $row = array();
        try {
            $this->load->database();

            $sql = "SELECT nickname FROM users WHERE open_id = '" . $open_id . "'";
            Log_Util::log_sql($sql, __CLASS__);

            $query = $this->db->query($sql);
            $row = $query->row_array();
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $row['nickname'];
    }

    public function verify_session_key($data)
    {
        $open_id = array_key_exists("open_id", $data) ? $data["open_id"] : null;
        $key = array_key_exists("key", $data) ? $data["key"] : null;

        try {
            $this->load->database();
            $sql = "select open_id from users where open_id = '" . $open_id . "' and session_key= '" . $key . "'";
            Log_Util::log_sql($sql, __CLASS__);
            $result = $this->db->query($sql);
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return ($result->num_rows() === 1) ? true : false;
    }

    public function query_user($open_id)
    {
        $row = array();
        try {
            $this->load->database();

            $sql = "SELECT * FROM users WHERE open_id = '" . $open_id . "'";
            Log_Util::log_sql($sql, __CLASS__);

            $query = $this->db->query($sql);
            $row = $query->row_array();
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $row;
    }

}