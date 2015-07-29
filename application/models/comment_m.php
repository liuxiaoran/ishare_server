<?php
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/20
 * Time: 11:36
 */
class comment_m extends CI_Model
{
    public function add($comment) {
        $sql0 = 'INSERT INTO comment(card_id, open_id, comment, rating, time) VALUES(?, ?, ? ,?, ?)';
        $param0 = array((int) $comment['card_id'], $comment['open_id'], $comment['comment'], (float) $comment['rating'], $comment['time']);
        $sql1 = 'UPDATE share_items SET rating_average = (rating_num * rating_average + ?) / (rating_num + 1)'
            . ' AND rating_num = (rating_num + 1) AND comment_num = (comment_num + 1)'
            . ' WHERE id = ?';
        $param1 = array((float) $comment['rating'], (int) $comment['card_id']);
        $sql2 = 'UPDATE share_items SET comment_num = (comment_num + 1) WHERE id = ?';
        $param2 = array((int) $comment['card_id']);
        $sql_array = array($sql0, $sql1, $sql2);
        $param_array = array($param0, $param1, $param2);
        return Base_Dao::query_for_trans($sql_array, $param_array);
    }

    public function get($card_id, $page_num, $page_size)
    {
        $sql = 'SELECT c.id, c.card_id, c.comment, c.rating, c.time, u.nickname, u.avatar, u.gender'
            . ' FROM comment AS c JOIN users AS u ON c.open_id = u.open_id'
            . ' WHERE c.card_id = ?'
            . ' GROUP BY c.open_id ORDER BY time DESC'
            . " LIMIT ?, ?";
        $offset = ($page_num - 1) * $page_size;
        $param = array((int) $card_id, (int) $offset, (int) $page_size);
        return Base_Dao::query_by_sql($sql, $param);
    }

    public function update($param, $id)
    {
        $table_name = 'comment';
        $update = $param;
        $where['id'] = $id;
        return Base_Dao::update($table_name, $update, $where);
    }

    public function delete($id, $card_id, $rating)
    {
        $sql0 = 'DELETE FROM comment WHERE id = ?';
        $param0 = array((int) $id);
        $sql1 = 'UPDATE share_items SET rating_average = (rating_num * rating_average - ?) / (rating_num - 1)'
            . ' AND rating_num = (rating_num - 1) AND comment_num = (comment_num - 1)'
            . ' WHERE id = ?';
        $param1 = array((float) $rating, (int) $card_id);
        $sql2 = 'UPDATE share_items SET comment_num = (comment_num - 1) WHERE id = ?';
        $param2 = array((int) $card_id);
        $sql_array = array($sql0, $sql1, $sql2);
        $param_array = array($param0, $param1, $param2);
        return Base_Dao::query_for_trans($sql_array, $param_array);
    }

}