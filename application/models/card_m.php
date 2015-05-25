<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');
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
        $this->load->model('Owner_location_m');
        $this->load->model('Record_m');
    }

    function add_card($card)
    {
        $status = false;
        try {
            $this->load->database();
            $this->db->trans_start();
            $this->db->insert('share_items', $card);
            if (array_key_exists('owner_available', $card)) {
                $this->OwnerLocation_M->add_locations($card['owner_available'], $card);
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

    public function delete_card($ids)
    {
        $status = true;
        try {
            $sql = "DELETE FROM share_items WHERE id = ?";
            $this->load->database();
            $this->db->trans_start();
            foreach ($ids as $id) {
                $this->db->query($sql, array($id));
                $this->OwnerLocation_M->delete_location(array($id));
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

    public function update_card($card)
    {
        $status = true;
        try {
            $sql = "UPDATE share_items SET id = " . $card['id'];
            if (array_key_exists('shop_name', $card)) {
                $sql = $sql . ", shop_name = " . $card['shop_name'];
            }
            if (array_key_exists('ware_type', $card)) {
                $sql = $sql . ", ware_type = " . $card['ware_type'];
            }
            if (array_key_exists('discount', $card)) {
                $sql = $sql . ", discount = " . $card['discount'];
            }
            if (array_key_exists('trade_type', $card)) {
                $sql = $sql . ", trade_type = " . $card['trade_type'];
            }
            if (array_key_exists('shop_location', $card)) {
                $sql = $sql . ", shop_location = " . $card['shop_location'];
            }
            if (array_key_exists('description', $card)) {
                $sql = $sql . ", description = " . $card['description'];
            }
            if (array_key_exists('img', $card)) {
                $sql = $sql . ", img = " . $card['img'];
            }
            if (array_key_exists('share_type', $card)) {
                $sql = $sql . ", share_type = " . $card['share_type'];
            }
            $sql = $sql . " WHERE id = " . $card['id'];

            $this->load->database();
            $this->db->trans_start();
            $this->db->query($sql);
            $this->OwnerLocation_M->deleteLocation(array($card['id']));
            if (array_key_exists('owner_available', $card)) {
                $this->OwnerLocation_M->add_locations($card['owner_available'], $card);
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }


        return $status;
    }

    public function query_by_phone($phone, $page_num, $page_size)
    {
        $sql = null;
        try {
            $offset = ($page_num - 1) * $page_size;
            $sql = 'SELECT * FROM share_item WHERE owner = ' . $phone . "ORDER By time LIMIT $offset, $page_size";
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $this->query_card($sql);
    }

    public function query_sort_composite($keyword, $lng, $lat, $page_num, $page_size)
    {
        $data = $this->Owner_location_m->get_near_sort_composite($keyword, $lng, $lat, $page_num, $page_size);
        return $this->query_cards($data, $lng, $lat);
    }

    public function query_sort_discount($keyword, $trade_type, $lng, $lat, $page_num, $page_size)
    {
        $data = $this->Owner_location_m->get_near_sort_discount($keyword, $trade_type, $lng, $lat, $page_num, $page_size);
        return $this->query_cards($data, $lng, $lat);
    }

    public function set_card($row)
    {
        $item = array();
        $item['card_id'] = $row['id'];
        $item['owner_id'] = $row['owner'];
        $item['owner_name'] = $row['nickname'];
        $item['owner_avatar'] = $row['avatar'];
        $item['shop_name'] = $row['shop_name'];
        $item['ware_type'] = $row['ware_type'];
        $item['discount'] = $row['discount'];
        $item['trade_type'] = $row['trade_type'];
        $item['shop_location'] = $row['shop_location'];
        $item['shop_longitude'] = $row['shop_longitude'];
        $item['shop_latitude'] = $row['shop_latitude'];
        $item['description'] = $row['description'];
        $item['img'] = $row['img'];
        $item['share_type'] = $row['share_type'];
        $item['publish_time'] = $row['time'];

        return $item;
    }

    public function query_by_id($id)
    {
        $item = null;
        try {
            $sql = 'SELECT share_items.id, share_items.owner, share_items.shop_name, share_items.ware_type,'
                . ' share_items.discount, share_items.trade_type, share_items.shop_location,'
                . ' share_items.shop_longitude, share_items.shop_latitude, share_items.description,'
                . ' share_items.img, share_items.share_type, share_items.time, users.nickname, users.avatar'
                . ' FROM share_items, users WHERE users.phone = share_items.owner'
                . ' AND share_items.id = ' . $id;

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();

            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $item = $this->set_card($row);
            }
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $item;
    }

    public function query_cards($data, $lng, $lat)
    {
        $items = array();
        try {
            foreach ($data as $location) {
                $item = $this->query_by_id($location['item_id']);
                if ($item['shop_longitude'] == null || $item['shop_latitude'] == null) {
                    $item['shop_distance'] = -1;
                } else {
                    $item['shop_distance'] =
                        Distance_Util::get_kilometers_between_points($lng, $lat, $item['shop_longitude'], $item['shop_latitude']);
                }
                $item['owner_longitude'] = $location['longitude'];
                $item['owner_latitude'] = $location['latitude'];
                $item['owner_location'] = $location['location'];
                $item['owner_time'] = $location['time'];
                $item['distance'] = $location['distance'];
                array_push($items, $item);
            }
        } catch (Exception $e) {
            $items = array();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $items;
    }

    public function query_sort_distance($keyword, $trade_type, $lng, $lat, $page_num, $page_size)
    {
        $data = $this->Owner_location_m->get_near_sort_distance($keyword, $trade_type, $lng, $lat, $page_num, $page_size);
        return $this->query_cards($data, $lng, $lat);
    }

    public function query_card($sql)
    {
        $cards = array();
        try {
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();

            foreach ($query->result_array() as $row) {
                array_push($cards, $row);
            }
        } catch (Exception $e) {
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $cards;
    }
}