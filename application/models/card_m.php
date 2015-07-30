<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
require_once(dirname(__FILE__) . '/../util/Distance_Util.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/9
 * Time: 14:11
 */
class Card_m extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function add_card($card)
    {
        $table_name = 'share_items';
        $param = $card;
        return Base_Dao::insert($table_name, $param);
    }

    public function delete_card($ids)
    {
        $table_name = 'share_items';
        $param['id'] = $ids;
        return Base_Dao::delete($table_name, $param);
    }

    public function update_card($card)
    {
        $table_name = 'share_items';
        $update = $card;
        $where['id'] = $card['id'];
        return Base_Dao::update($table_name, $update, $where);
    }

    public function query_i_share($open_id, $page_num, $page_size)
    {
        $offset = ($page_num - 1) * $page_size;
        $sql = "SELECT * FROM share_items WHERE owner = ? ORDER BY time LIMIT ?, ?";
        $param = array($open_id, (int) $offset, (int) $page_size);
        $data =  Base_Dao::query_by_sql($sql, $param);
        $result = array();
        foreach ($data as $item) {
            $item['img'] = json_decode($item['img']);
            array_push($result, $item);
        }
        return $result;
    }

    public function query_sort_composite($trade_type, $lng, $lat, $page_num, $page_size)
    {
        $offset = ($page_num - 1) * $page_size;
        $param = array((float) $lng, (float) $lat, (int) $offset, (int) $page_size);
        $sql = 'SELECT S.id, S.owner, S.shop_name, S.ware_type, S.discount,'
            . ' S.trade_type, S.shop_location, S.shop_longitude, S.shop_latitude,'
            . ' S.description, S.img, S.time, U.nickname, U.avatar, U.gender,'
            . ' S.rating_average, S.rating_num, S.lend_count, L.longitude,'
            . ' L.latitude, L.location, S.time,'
            . ' ((POWER(MOD(ABS(longitude - ?),360),2) + POWER(ABS(latitude - ?),2)) * discount) AS distance'
            . ' FROM share_items S, location L, users U'
            . ' WHERE S.location_id = L.id';
        if($trade_type != 0) {
            $sql = $sql . ' AND S.trade_type = ?';
            $param = array((float) $lng, (float) $lat, (int) $trade_type, (int) $offset, (int) $page_size);
        }
        $sql = $sql . ' AND U.open_id = S.owner ORDER BY distance LIMIT ?, ?';

        $result = Base_Dao::query_by_sql($sql, $param);
        return $this->result_processing($result, $lng, $lat);
    }

    public function query_sort_discount($trade_type, $lng, $lat, $page_num, $page_size) {
        $offset = ($page_num - 1) * $page_size;
        $param = array((float) $lng, (float) $lat, (int) $offset, (int) $page_size);
        $sql = 'SELECT S.id, S.owner, S.shop_name, S.ware_type, S.discount,'
            . ' S.trade_type, S.shop_location, S.shop_longitude, S.shop_latitude,'
            . ' S.description, S.img, S.time, U.nickname, U.avatar, U.gender,'
            . ' S.rating_average, S.rating_num, S.lend_count, L.longitude, L.latitude, L.location, S.time,'
            . ' (POWER(MOD(ABS(longitude - ?),360),2) + POWER(ABS(latitude - ?),2)) AS distance'
            . ' FROM location L, share_items S, users U'
            . ' WHERE L.id = S.location_id AND S.owner = U.open_id';
        if($trade_type != 0) {
            $sql = $sql . ' AND S.trade_type = ?';
            $param = array((float) $lng, (float) $lat, (int) $trade_type, (int) $offset, (int) $page_size);
        }
        $sql = $sql . ' ORDER BY S.discount LIMIT ?, ?';

        Log_Util::log_sql($sql, __CLASS__);

        $result = Base_Dao::query_by_sql($sql, $param);
        return $this->result_processing($result, $lng, $lat);
    }

    public function query_sort_distance($trade_type, $lng, $lat, $page_num, $page_size)
    {
        $offset = ($page_num - 1) * $page_size;
        $param = array((float) $lng, (float) $lat, (int) $offset, (int) $page_size);
        $sql = 'SELECT S.id, S.owner, S.shop_name, S.ware_type, S.discount,'
            . ' S.trade_type, S.shop_location, S.shop_longitude, S.shop_latitude,'
            . ' S.description, S.img, S.time, U.nickname, U.avatar , U.gender,'
            . ' S.rating_average, S.rating_num, S.lend_count, L.longitude,'
            . ' L.latitude , L.location , S.time , S.rating_average, S.rating_num, S.lend_count,'
            . ' (POWER(MOD(ABS(longitude - ?),360),2) + POWER(ABS(latitude - ?),2)) AS distance'
            . ' FROM share_items S, location L, users U '
            . ' WHERE S.location_id = L.id';
        if($trade_type != 0) {
            $sql = $sql . ' AND S.trade_type = ?';
            $param = array((float) $lng, (float) $lat, (int) $trade_type, (int) $offset, (int) $page_size);
        }
        $sql = $sql . ' AND U.open_id = S.owner ORDER BY distance LIMIT ?, ?';

        $result = Base_Dao::query_by_sql($sql, $param);
        $result = $this->result_processing($result, $lng, $lat);
        return $result;
    }

    public function result_processing($data, $lng, $lat) {
        $result = array();
        foreach($data as $item) {
            $card['card_id'] = $item['id'];
            $card['owner_name'] = $item['nickname'];
            $card['owner_avatar'] = $item['avatar'];
            $card['owner_id'] = $item['owner'];
            $card['gender'] = $item['gender'];
            $card['shop_name'] = $item['shop_name'];
            $card['ware_type'] = $item['ware_type'];
            $card['discount'] = $item['discount'];
            $card['trade_type'] = $item['trade_type'];
            $card['shop_location'] = $item['shop_location'];
            $card['shop_longitude'] = $item['shop_longitude'];
            $card['shop_latitude'] = $item['shop_latitude'];
            $card['shop_distance'] =
                Distance_Util::get_kilometers_between_points($lng, $lat, $item['shop_longitude'], $item['shop_latitude']);
            $card['description'] = $item['description'];
            $card['img'] = json_decode($item['img']);
            $card['publish_time'] = $item['time'];
            $card['owner_longitude'] = $item['longitude'];
            $card['owner_latitude'] = $item['latitude'];
            $card['available_addr'] = $item['location'];
            $card['owner_distance'] = $item['distance'];
            $card['rating_average'] = $item['rating_average'];
            $card['rating_num'] = $item['rating_num'];
            $card['lend_count'] = $item['lend_count'];

            // 距离保留一位小数
            $card['shop_distance'] = round($card['shop_distance'], 1);
            $card['owner_distance'] = round($card['owner_distance'], 1);
            array_push($result, $card);
        }
        return $result;
    }

    public function tans_images($image_str)
    {
        $images = (Array)json_decode($image_str);
        return $images;
    }

    public function query_by_id($id)
    {
        $sql = 'SELECT share_items.id, share_items.owner, share_items.shop_name, share_items.ware_type,'
            . ' share_items.discount, share_items.trade_type, share_items.shop_location,'
            . ' share_items.shop_longitude, share_items.shop_latitude, share_items.description,'
            . ' share_items.img, share_items.time, users.nickname, users.avatar, users.gender,'
            . ' share_items.rating_average, share_items.rating_num, share_items.lend_count' // 添加用户的评分信息
            . ' FROM share_items, users WHERE users.open_id = share_items.owner'
            . ' AND share_items.id = ?';
        $param = array($id);
        $result =  Base_Dao::query_one_by_sql($sql, $param);
        $result['img'] = json_decode($result['img']);
        return $result;
    }

    public function query($id, $borrow_id, $lend_id) {
        $sql = 'SELECT s.id AS card_id, s.owner, s.shop_name, s.ware_type, s.discount, s.trade_type, s.shop_location,'
            . ' s.shop_longitude, s.shop_latitude, s.description, s.img AS shop_img, s.time, l.open_id AS lend_open_id,'
            . ' l.nickname AS lend_nickname, l.avatar AS lend_avatar, l.gender AS gender, s.rating_average,'
            . ' s.rating_num, s.lend_count, b.open_id AS borrow_open_id, b.nickname AS borrow_name,'
            . ' b.avatar AS borrow_avatar, b.gender AS borrow_gender'
            . ' FROM share_items AS s, users AS b, users AS l WHERE l.open_id = s.owner'
            . ' AND s.id = ? AND l.open_id = ? AND b.open_id = ?';
        $param = array((int) $id, $lend_id, $borrow_id);
        $result =  Base_Dao::query_one_by_sql($sql, $param);
        $result['shop_img'] = json_decode($result['shop_img']);
        $result['type'] = 1;
        return $result;
    }

    public function search($key, $lng, $lat, $page_num, $page_size)
    {
        $offset = ($page_num - 1) * $page_size;
        $param = array((float) $lng, (float) $lat,$key, (int) $offset, (int) $page_size);
        $sql = "SELECT S.id, S.owner, S.shop_name, S.ware_type, S.discount,"
            . " S.trade_type, S.shop_location, S.shop_longitude, S.shop_latitude,"
            . " S.description, S.img, S.time, U.nickname, U.avatar, U.gender,"
            . " S.rating_average, S.rating_num, S.lend_count, L.longitude,"
            . " L.latitude , L.location , S.time , S.rating_average, S.rating_num, S.lend_count,"
            . " (POWER(MOD(ABS(longitude - ?),360),2) + POWER(ABS(latitude - ?),2)) AS distance"
            . " FROM share_items S, location L, users U"
            . " WHERE S.location_id = L.id AND S.shop_name LIKE CONCAT('%', ?, '%')"
            . " AND U.open_id = S.owner LIMIT ?, ?";

        $result = Base_Dao::query_by_sql($sql, $param);
        $result = $this->result_processing($result, $lng, $lat);
        return $result;
    }
}