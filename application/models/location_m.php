<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/23
 * Time: 12:03
 */
class location_m extends CI_Model
{
    private $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new Base_Dao();
    }

    public function add($location)
    {
        $table_name = 'location';
        return $this->dao->insert($table_name, $location);
    }

    public function delete($id)
    {
        $table_name = 'location';
        $param['id'] = $id;
        return $this->dao->delete($table_name, $param);
    }

    public function update($param, $id)
    {
        $table_name = 'location';
        $where['id'] = $id;
        return $this->dao->update($table_name, $param, $where);
    }

    public function get($open_id)
    {
        $table_name = 'location';
        $select = 'id, longitude, latitude, location';
        $where['open_id'] = $open_id;
        return $this->dao->query($table_name, $select, $where);
    }
}