<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/11
 * Time: 10:40
 */
class User_m extends CI_Model
{
    private $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new Base_Dao();
    }

    public function login($open_id)
    {
        $table_name = 'users';
        $select = 'phone, nickname, avatar, gender, real_name,'
            .' per_photo, id_facade, id_back, work_unit, work_card';
        $where['open_id'] = $open_id;
        return $this->dao->query_by_id($table_name, $select, $where);
    }

    public function get_session_key($open_id)
    {
        $table_name = 'users';
        $key = md5($open_id . time());
        $param['session_key'] = $key;
        $where['open_id'] = $open_id;
        $result = $this->dao->update($table_name, $param, $where);
        return $result? $key : null;
    }

    public function add_user($user)
    {
        $table_name = 'users';
        return $this->dao->insert($table_name, $user);
    }

    public function update_user_info($user)
    {
        $table_name = 'users';
        $where['open_id'] = $user['open_id'];
        return $this->dao->update($table_name, $user, $where);
    }

    public function verify_session_key($data)
    {
        $table_name = 'users';
        $select = 'open_id';
        $where['open_id'] = $data['open_id'];
        $where['session_key'] = $data['key'];
        $result = $this->dao->query_by_id($table_name, $select, $where);
        return ($result == null) ? false : true;
    }

    public function update_credit($user)
    {
        $table_name = 'users';
        $where['open_id'] = $user['open_id'];
        return $this->dao->update($table_name, $user, $where);
    }

    public function query_by_id($open_id) {
        $sql = 'SELECT nickname, gender, avatar FROM users WHERE open_id = ?';
        $param = array($open_id);
        return $this->dao->query_one_by_sql($sql, $param);
    }

    public function query_phone_type($open_id) {
        $sql = 'SELECT phone_type FROM users WHERE open_id = ?';
        $param = array($open_id);
        return $this->dao->query_one_by_sql($sql, $param);
    }
}