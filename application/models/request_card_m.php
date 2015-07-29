<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
class Request_card_m extends CI_Model
{
    public function add($param)
    {
        $table_name = 'request_card';
        return Base_Dao::insert($table_name, $param);
    }

    public function get($longitude, $latitude, $page_num, $page_size)
    {
        $sql = 'SELECT R.id, R.open_id AS owner_id, R.shop_name, R.shop_location, R.shop_longitude,'
            . ' R.shop_latitude, R.time AS publish_time, R.discount, R.ware_type, '
            . ' R.trade_type, R.description, R.user_longitude, R.user_latitude,'
            . ' U.nickname, U.avatar, U.gender,'
            . ' (POWER(MOD(ABS(longitude - ?),360),2) + POWER(ABS(latitude - ?),2)) AS distance,'
            . ' (POWER(MOD(ABS(shop_longitude - ?),360),2) + POWER(ABS(shop_latitude - ?),2)) AS shop_distance'
            . ' FROM request_card R, location L, users U'
            . ' WHERE R.open_id = U.open_id ORDER BY distance ASC LIMIT ?, ?';
        $offset = $page_size * ($page_num - 1);
        $param = array((float) $longitude, (float) $latitude, (float) $longitude, (float) $latitude, (int) $offset, (int) $page_size);
        $result =  Base_Dao::query_by_sql($sql, $param);
        return $this->result_processing($result);
    }

    public function get_near($longitude, $latitude, $page_num, $page_size, $kilometer)
    {
        $longitude_near = $this->get_near_latitude($kilometer);
        $latitude_near = $this->get_near_latitude($kilometer);
        $sql = 'SELECT R.id, R.open_id AS owner_id, R.shop_name, R.shop_location, R.shop_longitude,'
            . ' R.shop_latitude, R.time AS publish_time, R.discount, R.ware_type, '
            . ' R.trade_type, R.description, R.longitude, R.latitude,'
            . ' U.nickname, U.avatar, U.gender,'
            . ' (POWER(MOD(ABS(longitude - ?),360),2) + POWER(ABS(latitude - ?),2)) AS distance,'
            . ' (POWER(MOD(ABS(shop_longitude - ?),360),2) + POWER(ABS(shop_latitude - ?),2)) AS shop_distance'
            . ' FROM request_card R, location L, users U'
            . ' WHERE R.location_id = L.id AND R.open_id = U.open_id'
            . ' AND (shop_longitude - ?) < ? AND (shop_latitude -?) < ?'
            . ' ORDER BY R.time ASC LIMIT ?, ?';
        $offset = $page_size * ($page_num - 1);
        $param = array((float) $longitude, (float) $latitude, (float) $longitude,
            (float) $latitude, (float) $longitude, (float) $longitude_near,
            (float) $latitude, (float) $latitude_near, (int) $offset, (int) $page_size);
        $result =  Base_Dao::query_by_sql($sql, $param);
        return $this->result_processing($result);
    }

    public function get_near_longitude($type) {
        switch($type) {
            case 3: $longitude = 0.026951; break;
            case 5: $longitude = 0.044918; break;
            case 10:$longitude = 0.089835; break;
        }
        return $longitude;
    }

    public function get_near_latitude($type) {
        switch($type) {
            case 3: $longitude = 0.027040; break;
            case 5: $longitude = 0.045066; break;
            case 10:$longitude = 0.090132; break;
        }
        return $longitude;
    }

    public function result_processing($data) {
        foreach($data as $item) {
            $item['owner_distance'] = round($item['distance'], 1); // 距离保留1位小数
            $item['shop_distance'] = round($item['shop_distance'], 1);
        }
        return $data;
    }

    public function update($param, $id)
    {
        $table_name = 'request_card';
        $update = $param;
        $where['id'] = $id;
        return Base_Dao::update($table_name, $update, $where);
    }

    public function delete($id)
    {
        $table_name = 'request_card';
        $where['id'] = $id;
        return Base_Dao::delete($table_name, $where);
    }

    public function get_my_request($open_id, $page_num, $page_size) {
        $offset = ($page_num - 1) * $page_size;
        $sql = 'SELECT * FROM request_card WHERE open_id = ? ORDER BY time DESC LIMIT ?, ?';
        $param = array($open_id, (int) $offset, (int) $page_size);
        return Base_Dao::query_by_sql($sql, $param);
    }

    public function query($id, $borrow_id, $lend_id) {
        $sql = 'SELECT rc.id, rc.open_id, rc.shop_name, rc.ware_type, rc.discount, rc.trade_type, rc.shop_location,'
            . ' rc.shop_longitude, rc.shop_latitude, rc.description, rc.time, l.open_id AS lend_open_id,'
            . ' l.nickname AS lend_nickname, l.avatar AS lend_avatar, l.gender AS gender, s.rating_average,'
            . ' s.rating_num, s.lend_count, b.open_id AS borrow_open_id, b.nickname AS borrow_name,'
            . ' b.avatar AS borrow_avatar, b.gender AS borrow_gender'
            . ' FROM request_card AS rc, users AS b, users AS l WHERE b.open_id = rc.owner'
            . ' AND s.id = ? AND l.open_id = ? AND b.open_id = ?';
        $param = array((int) $id, $lend_id, $borrow_id);
        return Base_Dao::query_one_by_sql($sql, $param);
    }

    public function get_request($id) {
        $sql = 'SELECT * FROM request_card WHERE id = ? ORDER BY time DESC';
        $param = array($id);
        return Base_Dao::query_one_by_sql($sql, $param);
    }
}