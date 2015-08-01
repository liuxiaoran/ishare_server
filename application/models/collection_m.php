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
            $id = -2;
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

    public function get($open_id, $page_num, $page_size)
    {
        $sql = 'SELECT s.id, s.owner, s.shop_name, s.ware_type, s.discount, s.trade_type, s.shop_location,'
            . ' s.shop_longitude, s.shop_latitude, s.img, s.rating_average, s.rating_num, s.comment_num, s.lend_count'
            . ' FROM share_items AS s JOIN collection AS c ON s.id = c.card_id'
            . " WHERE c.open_id = ? ORDER BY c.time DESC"
            . " LIMIT ?, ?";
        $offset = ($page_num - 1) * $page_size;
        $param = array($open_id, (int) $offset, (int) $page_size);
        return $this->dao->query_by_sql($sql, $param);
    }
}