<?php

class Comment_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add($comment)
    {
        $add_status = false;
        try {
            $this->load->database();
            $this->db->insert('user_comment', $comment);
            if ($this->update_rating($comment)) // 更新该卡记录的分数
                $add_status = true;
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
        }
        return $add_status;
    }

    public function get($paras)
    {
        $begin_index = ($paras['page_num'] - 1) * $paras['page_size'];
        $end_index = $paras['page_size'];
        $sql = " SELECT UC.id, UC.card_id, UC.comment, UC.rating, UC.time, U.nickname, U.avatar, U.gender"
             . " FROM user_comment AS UC JOIN users AS U ON UC.open_id = U.open_id"
             . " WHERE UC.card_id = ?"
             . " LIMIT $begin_index, $end_index";
        try {
            $this->load->database();
            $query = $this->db->query($sql, array($paras['card_id']));
            $this->db->close();
            return $query->result_array();
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($paras, $id)
    {
        try {
            $this->load->database();
            $this->db->update('user_comment', $paras, array('id' => $id));
            $this->db->close();
            return true;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $this->load->database();
            $this->db->delete('user_comment', array('id' => $id));
            $this->db->close();
            return true;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    private function update_rating($comment)
    {
        $select_sql = " SELECT rating_average, rating_num"
                    . " FROM share_items"
                    . " WHERE id = ?";
        $update_sql = " UPDATE share_items"
                    . " SET rating_average = ?, rating_num = rating_num + 1"
                    . " WHERE id = ?";
        try {
            $query = $this->db->query($select_sql, array($comment['card_id']));
            if ($query->num_rows() > 0)
            {
                $result = $query->row_array();
                $new_rating = ($result['rating_average'] * $result['rating_num'] + $comment['rating']) / ($result['rating_num'] + 1);
                $new_rating = round($new_rating, 3);

                $this->db->query($update_sql, array($new_rating, $comment['card_id']));
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}