<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/23
 * Time: 15:45
 */
class collection_m extends CI_Model
{

    public function add($collection)
    {
        $id = -1;
        try {
            $this->load->database();
            $sql = "SELECT id FROM collection WHERE open_id = '" . $collection['open_id']
                . "' AND card_id = " . $collection['card_id'];
            Log_Util::log_sql($sql, __CLASS__);
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $id = -2;
            } else {
                $this->db->insert('collection', $collection);
                $id = $this->db->insert_id();
            }
            $this->db->close();
        } catch (Exception $e) {
            $id = -1;
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
            $this->db->close();
        }

        return $id;
    }

    public function delete($ids)
    {
        $status = false;
        try {
            $this->load->database();
            $this->db->trans_start();
            foreach ($ids as $id) {
                $this->db->delete('collection', array('id' => $id));
//                $sql = 'DELETE FROM collection WHERE id = ' . $id;
//                Log_Util::log_sql($sql, __CLASS__);
//                $this->db->query($sql);
            }
            $this->db->trans_complete();

            $status = $this->db->trans_status();
            $this->db->close();

        } catch (Exception $e) {
            $status = false;
            $this->db->close();
        }
        return $status;
    }

    public function get($open_id, $page_num, $page_size)
    {
        $comments = array();
        try {
            $this->load->database();
            $offset = ($page_num - 1) * $page_size;
            $sql = 'SELECT s.id, s.owner, s.shop_name, s.ware_type, s.discount, s.trade_type, s.shop_location,'
                . ' s.shop_longitude, s.shop_latitude, s.img, s.rating_average, s.rating_num, s.comment_num, s.lend_count'
                . ' FROM share_items AS s JOIN collection AS c ON s.id = c.card_id'
                . " WHERE c.open_id = '" . $open_id
                . "' ORDER BY c.time DESC"
                . " LIMIT $offset, $page_size";
            Log_Util::log_sql($sql, __CLASS__);
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    array_push($comments, $row);
                }
            }
            $this->db->close();
        } catch (Exception $e) {
            $comments = array();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
            $this->db->close();
        }
        return $comments;
    }
}