<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../util/Distance_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/11
 * Time: 16:30
 */
class Owner_location_m extends CI_Model
{

    public function add_locations($locations, $card)
    {
        $status = true;
        $locations = (Array) json_decode($locations);
        Log_Util::log_param($locations, __CLASS__);
        try {
            $this->load->database();
            $this->db->trans_start();
            foreach ($locations as $location) {
                $location->trade_type = $card['trade_type'];
                $location->discount = $card['discount'];
                $location->search = $card['shop_name'] . " " . $card['shop_location'] . " " . $card['description'];
                $location->item_id = $card['id'];
                $this->db->insert('owner_location', $location);
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function trans_data($location, $card) {
        $item = array();
        $item->location = $location['location'];
        $item->time = $location['time'];
        $item->longitude = $location['longitude'];
        $item->latitude = $location['latitude'];
        $item->phone = $location['phone'];
        $item->name = $location['name'];
        $item->trade_type = $card['trade_type'];
        $item->shop_name = $card['shop_name'] . " " . $card['shop_location'] . " " . $card['description'];

        return $item;
    }

    public function delete_location($ids)
    {
        $status = true;
        try {
            $this->load->database();
            $sql = "DELETE FROM owner_location WHERE id = ?";
            $this->db->trans_start();
            foreach ($ids as $id) {
                $this->db->query($sql, array($id));
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function get_owner_location($item_id)
    {
        $data = array();
        try {
            $this->load->database();
            $sql = "SELECT * FROM owner_location WHERE item_id = ?";
            $query = $this->db->query($sql, array($item_id));
            $data = array();
            foreach ($query->result_array() as $row) {
                $location = array();
                $location['id'] = $row->id;
                $location['item_id'] = $row->item_id;
                $location['longitude'] = $row->longitude;
                $location['latitude'] = $row->latitude;
                $location['location'] = $row->location;
                $location['time'] = $row->time;
                array_push($data, $location);
            }
            $this->db->close();
        } catch (Exception $e) {
            $data = array();
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $data;
    }

    public function get_near_sort_composite($keyword, $lng, $lat, $pageNum, $pageSize, $range = 1)
    {
        $data = array();
        try {
            $this->load->database();

            $offset = ($pageNum - 1) * $pageSize;
            $sql = "SELECT item_id, longitude, latitude, location, time,"
                . " ((POWER(MOD(ABS(longitude - $lng),360),2) + POWER(ABS(latitude - $lat),2)) * discount) AS composite,"
                . " count(DISTINCT item_id)"
                . " from owner_location";
//            $sql = $sql." WHERE longitude > ".($lng + $range)." AND longitude < ".($lng - $range);
//            $sql = $sql." AND latitude < ".($lat + $range)." AND latitude > ".($lat - $range);
            if ($keyword != null) {
                $sql = $sql . " AND search LIKE '%" . $keyword . "%'";
            }
//            if($trade_type != -1) {
//                $sql = $sql." AND trade_type = $trade_type";
//            }
            $sql = $sql . " GROUP by item_id ORDER by composite LIMIT $offset, $pageSize";

            Log_Util::log_sql($sql, __CLASS__);

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $location = array();
                $location['item_id'] = $row['item_id'];
                $location['longitude'] = $row['longitude'];
                $location['latitude'] = $row['latitude'];
                $location['location'] = $row['location'];
                $location['time'] = $row['time'];
                $location['distance'] =
                    Distance_Util::get_kilometers_between_points($lng, $lat, $location['longitude'], $location['latitude']);
                array_push($data, $location);
            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            $data = array();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }


        return $data;
    }

    public function get_near_sort_distance($keyword, $trade_type, $lng, $lat, $pageNum, $pageSize, $range = 1)
    {
        $data = array();
        try {
            $this->load->database();

            $offset = ($pageNum - 1) * $pageSize;
            $sql = "SELECT item_id, longitude, latitude, location, time,"
                . " (POWER(MOD(ABS(longitude - $lng),360),2) + POWER(ABS(latitude - $lat),2)) AS distance,"
                . " count(DISTINCT item_id)"
                . " from owner_location WHERE 1 = 1";
//            $sql = $sql." AND longitude > ".($lng + $range)." AND longitude < ".($lng - $range);
//            $sql = $sql." AND latitude < ".($lat + $range)." AND latitude > ".($lat - $range);
            if ($keyword != null) {
                $sql = $sql . " AND search LIKE '%" . $keyword . "%'";
            }
            if ($trade_type != -1) {
                $sql = $sql . " AND trade_type = $trade_type";
            }
            $sql = $sql . " GROUP by item_id ORDER BY distance LIMIT $offset, $pageSize";

            Log_Util::log_sql($sql, __CLASS__);

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $location = array();
                $location['item_id'] = $row['item_id'];
                $location['longitude'] = $row['longitude'];
                $location['latitude'] = $row['latitude'];
                $location['location'] = $row['location'];
                $location['time'] = $row['time'];
                $location['distance'] =
                    Distance_Util::get_kilometers_between_points($lng, $lat, $location['longitude'], $location['latitude']);
                array_push($data, $location);
            }
            $this->db->close();
        } catch (Exception $e) {
            $data = array();
            $this->db->close();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }


        return $data;
    }

    public function get_near_sort_discount($keyword, $trade_type, $lng, $lat, $pageNum, $pageSize, $range = 1)
    {
        $data = array();
        try {
            $this->load->database();

            $offset = ($pageNum - 1) * $pageSize;
            $sql = "SELECT O.item_id, O.longitude, O.latitude, O.location, O.time, count(DISTINCT O.item_id)"
                . " FROM owner_location AS O JOIN share_items AS S ON O.item_id = S.id";
//            $sql = $sql." AND longitude > ".($lng + $range)." AND longitude < ".($lng - $range);
//            $sql = $sql." AND latitude < ".($lat + $range)." AND latitude > ".($lat - $range);
            if ($keyword != null) {
                $sql = $sql . " AND O.search LIKE '%" . $keyword . "%'";
            }
            if ($trade_type != -1) {
                $sql = $sql . " AND S.trade_type = $trade_type";
            }
            $sql = $sql . " GROUP BY O.item_id ORDER BY S.discount LIMIT $offset, $pageSize";

            Log_Util::log_sql($sql, __CLASS__);

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $location = array();
                $location['item_id'] = $row['item_id'];
                $location['longitude'] = $row['longitude'];
                $location['latitude'] = $row['latitude'];
                $location['location'] = $row['location'];
                $location['time'] = $row['time'];
                if ($location['longitude'] == null || $location['latitude'] == null) {
                    $location['distance'] = -1;
                } else {
                    $location['distance'] =
                        Distance_Util::get_kilometers_between_points($lng, $lat, $location['longitude'], $location['latitude']);
                }

                array_push($data, $location);
            }
            $this->db->close();
        } catch (Exception $e) {
            $data = array();
            $this->db->close();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $data;
    }
}