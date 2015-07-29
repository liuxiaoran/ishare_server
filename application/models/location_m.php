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

    public function add($location)
    {
        $table_name = 'location';
        return Base_Dao::insert($table_name, $location);
    }

    public function delete($id)
    {
        $table_name = 'location';
        $param['id'] = $id;
        return Base_Dao::delete($table_name, $param);
    }

    public function update($param, $id)
    {
        $table_name = 'location';
        $where['id'] = $id;
        return Base_Dao::update($table_name, $param, $where);
    }

    public function get($open_id)
    {
        $table_name = 'location';
        $select = 'id, longitude, latitude, location';
        $where['open_id'] = $open_id;
        return Base_Dao::query($table_name, $select, $where);
    }
}