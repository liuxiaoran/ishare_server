<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/20
 * Time: 11:36
 */
class comment_m extends CI_Model
{

    public function add($comment)
    {
        $id = 0;
        try {
            $this->load->database();
            $this->db->trans_start();
            $this->db->insert('comment', $comment);
            $id = $this->db->insert_id();
            if ($comment['rating'] != 0) {
                $sql = 'UPDATE share_items SET rating_average = (rating_num * rating_average + ' . $comment['rating'] . ') / (rating_num + 1)'
                    . ' AND rating_num = (rating_num + 1) AND comment_num = (comment_num + 1)'
                    . ' WHERE id = ' . $comment['card_id'];
            } else {
                $sql = 'UPDATE share_items SET comment_num = (comment_num + 1)'
                    . ' WHERE id = ' . $comment['card_id'];
            }

            Log_Util::log_sql($sql, __CLASS__);
            $this->db->query($sql);
            $this->db->trans_complete();

            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $id = 0;
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
            $this->db->close();
        }

        return $status ? $id : 0;
    }

    public function get($card_id, $page_num, $page_size)
    {
        $comments = array();
        try {
            $this->load->database();
            $offset = ($page_num - 1) * $page_size;
            $sql = 'SELECT c.id, c.card_id, c.comment, c.rating, c.time, u.nickname, u.avatar, u.gender'
                . ' FROM comment AS c JOIN users AS u ON c.open_id = u.open_id'
                . ' WHERE c.card_id = ' . $card_id
                . ' GROUP BY c.open_id ORDER BY time DESC'
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

    public function update($paras, $id)
    {
        try {
            $this->load->database();
            $this->db->update('comment', $paras, array('id' => $id));
            $this->db->close();
            return true;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    public function delete($id, $card_id, $rating)
    {
        $status = false;
        try {
            $this->load->database();
            $this->db->trans_start();
            $this->db->delete('comment', array('id' => $id));
            if ($rating != 0) {
                $sql = 'UPDATE share_items SET rating_average = (rating_num * rating_average - ' . $rating . ') / (rating_num - 1)'
                    . ' AND rating_num = (rating_num - 1) AND comment_num = (comment_num - 1)'
                    . ' WHERE id = ' . $card_id;
            } else {
                $sql = 'UPDATE share_items SET comment_num = (comment_num - 1)'
                    . ' WHERE id = ' . $card_id;
            }

            Log_Util::log_sql($sql, __CLASS__);
            $this->db->query($sql);
            $this->db->trans_complete();
            $status = $this->db->trans_status();

            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            $this->db->close();
        }

        return $status;
    }

}