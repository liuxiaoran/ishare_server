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
        $locations = json_encode($locations);
        Log_Util::log_param($locations, __CLASS__);
        try {
            $this->load->database();
            $this->db->trans_start();
            foreach ($locations as $location) {
                $location['trade_type'] = $card['trade_type'];
                $location['shop_name'] = $card['shop_name'] . " " . $card['shop_location'] . " " . $card['description'];
                $this->db->insert('owner_location', $location);
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function delete_location($ids)
    {
        $status = true;
        try {
            $sql = "DELETE FROM owner_location WHERE id = ?";
            $this->load->database();
            $this->db->trans_start();
            foreach ($ids as $id) {
                $this->db->query($sql, array($id));
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
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
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $data;
    }

    public function get_near_sort_composite($keyword, $lng, $lat, $pageNum, $pageSize, $range = 1)
    {
        $data = array();
        try {
            $offset = ($pageNum - 1) * $pageSize;
            $sql = "SELECT item_id, longitude, latitude, location, time,"
                . " ((POWER(MOD(ABS(longitude - $lng),360),2) + POWER(ABS(latitude - $lat),2)) * discount) AS value,"
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
            $sql = $sql . " GROUP by item_id ORDER by LIMIT $offset, $pageSize";

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $location = array();
                $location['item_id'] = $row['item_id'];
                $location['owner_longitude'] = $row['longitude'];
                $location['owner_latitude'] = $row['latitude'];
                $location['available_addr'] = $row['location'];
                $location['available_time'] = $row['time'];
                $location['owner_distance'] =
                    Distance_Util::get_kilometers_between_points($lng, $lat, $location['longitude'], $location['latitude']);
                array_push($data, $location);
            }
            $this->db->close();
        } catch (Exception $e) {
            $data = array();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }


        return $data;
    }

    public function get_near_sort_distance($keyword, $trade_type, $lng, $lat, $pageNum, $pageSize, $range = 1)
    {
        $data = array();
        try {
            $offset = ($pageNum - 1) * $pageSize;
            $sql = "SELECT item_id, longitude, latitude, location, time,"
                . " (POWER(MOD(ABS(longitude - $lng),360),2) + POWER(ABS(latitude - $lat),2)) AS distance,"
                . " count(DISTINCT item_id)"
                . " from owner_location";
//            $sql = $sql." WHERE longitude > ".($lng + $range)." AND longitude < ".($lng - $range);
//            $sql = $sql." AND latitude < ".($lat + $range)." AND latitude > ".($lat - $range);
            if ($keyword != null) {
                $sql = $sql . " AND search LIKE '%" . $keyword . "%'";
            }
            if ($trade_type != -1) {
                $sql = $sql . " AND trade_type = $trade_type";
            }
            $sql = $sql . " GROUP by item_id ORDER BY distance LIMIT $offset, $pageSize";

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
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
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }


        return $data;
    }

    public function get_near_sort_discount($keyword, $trade_type, $lng, $lat, $pageNum, $pageSize, $range = 1)
    {
        $data = array();
        try {
            $offset = ($pageNum - 1) * $pageSize;
            $sql = "SELECT item_id, longitude, latitude, location, time, count(DISTINCT item_id)"
                . " FROM owner_location";
//            $sql = $sql." WHERE longitude > ".($lng + $range)." AND longitude < ".($lng - $range);
//            $sql = $sql." AND latitude < ".($lat + $range)." AND latitude > ".($lat - $range);
            if ($keyword != null) {
                $sql = $sql . " AND search LIKE '%" . $keyword . "%'";
            }
            if ($trade_type != -1) {
                $sql = $sql . " AND trade_type = $trade_type";
            }
            $sql = $sql . " GROUP by item_id ORDER BY discount LIMIT $offset, $pageSize";

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();

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
        } catch (Exception $e) {
            $data = array();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
        }

        return $data;
    }
}