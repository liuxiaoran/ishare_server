<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/23
 * Time: 15:45
 */
class collection_m extends CI_Model
{
    private $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new Base_Dao();
    }

    public function add($collection)
    {
        $sql = "SELECT id FROM collection WHERE open_id = ? AND card_id = ?";
        $param = array($collection['open_id'], (int) $collection['card_id']);
        $result = $this->dao->query_by_sql($sql, $param);
        if(sizeof($result) > 0) {
            $id = -3;
        } else {
            $table_name = 'collection';
            $id = $this->dao->insert($table_name, $collection);
        }
        return $id;
    }

    public function delete($id)
    {
        $table_name = 'collection';
        $where['id'] = $id;
        return $this->dao->delete($table_name, $where);
    }

    public function get($open_id, $user_longitude, $user_latitude, $page_num, $page_size)
    {
        $sql = 'SELECT C.id AS collection_id, S.id AS card_id, S.owner AS owner_id,'
            . ' S.shop_name, S.ware_type, S.discount, S.service_charge,'
            . ' S.trade_type, S.shop_location, S.shop_longitude, S.shop_latitude,'
            . ' S.description, S.img, S.time, U.nickname AS owner_name, U.avatar AS owner_avatar, U.gender,'
            . ' S.rating_average, S.rating_num, S.lend_count, L.longitude AS owner_longitude,'
            . ' L.latitude AS owner_latitude, L.location AS available_addr,'
            . ' S.time AS publish_time, S.rating_average, S.rating_num, S.lend_count,'
            . ' (POWER(MOD(ABS(L.longitude - ?),360),2) + POWER(ABS(L.latitude - ?),2)) AS owner_distance,'
            . ' (POWER(MOD(ABS(S.shop_longitude - ?),360),2) + POWER(ABS(S.shop_latitude - ?),2)) AS shop_distance'
            . ' FROM share_items AS S, collection AS C, location L, users U '
            . ' WHERE C.open_id = ? AND S.id = C.card_id AND U.open_id = S.`owner` AND L.id = S.location_id'
            . ' ORDER BY C.time DESC LIMIT ?, ?';
        $offset = ($page_num - 1) * $page_size;
        $param = array((float)$user_longitude, (float)$user_latitude, (float)$user_longitude,
            (float)$user_latitude, $open_id, (int)$offset, (int)$page_size);
        return $this->dao->query_by_sql($sql, $param);
    }

    public function result_processing($data)
    {
        foreach ($data as $item) {
            $item['shop_distance'] = round($item['shop_distance'], 1);
            $item['owner_distance'] = round($item['owner_distance'], 1);
        }
        return $data;
    }
}