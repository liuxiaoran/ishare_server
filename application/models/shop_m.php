<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../util/Distance_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/18
 * Time: 14:56
 */
class Shop_m extends CI_Model
{

    public function query_sort_composite($keyword, $lng, $lat, $page_num, $page_size, $range = 1)
    {
        $shops = array();
        try {
            $this->load->database();
            $offset = ($page_num - 1) * $page_size;
            $sql = "SELECT DISTINCT(shop_name), shop_location, shop_longitude, shop_latitude  FROM share_items ";
//            $sql = $sql." WHERE shop_longitude < ".($lng + $range)." AND shop_longitude > ".($lng - $range);
//            $sql = $sql." AND shop_latitude < ".($lat + $range)." AND shop_latitude > ".($lat - $range);
            if ($keyword != null) {
                $sql = $sql . " AND (shop_name LIKE '%" . $keyword . "%' OR shop_location LIKE '%" . $keyword . "%";
            }
            $sql = $sql . " LIMIT $offset, $page_size";
            Log_Util::log_sql($sql, __CLASS__);

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $shop = array();
                $shop['shop_name'] = $row['shop_name'];
                $shop['shop_location'] = $row['shop_location'];
                $shop['shop_longitude'] = $row['shop_longitude'];
                $shop['shop_latitude'] = $row['shop_latitude'];
                if ($lng == null || $lat == null) {
                    $shop['shop_distance'] = 0;
                } else {
                    $shop['shop_distance'] =
                        Distance_Util::get_kilometers_between_points($lng, $lat, $shop['shop_longitude'], $shop['shop_latitude']);
                }

                array_push($shops, $shop);
            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            $shops = array();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $shops;
    }

    public function get_shop_image($data) {
        try {
            $this->load->database();

            $sql = 'SELECT shop_image FROM shop WHERE LIKE %' + $data['shop_name'] + '%';
            Log_Util::log_sql($sql, __CLASS__);

//          foreach($key_names as $key) {
//              $sql = $sql + '';
//          }
            $query = $this->db->query($sql);
            if(count($query->result_array()) == 1) {
                foreach($query->result_array() as $row) {
                    $result = $row['shop_image'];
                }
            } else {

            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $result;
    }
}