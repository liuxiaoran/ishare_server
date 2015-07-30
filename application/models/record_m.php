<?php
require_once(dirname(__FILE__) . '/../util/Distance_Util.php');
require_once(dirname(__FILE__) . '/../util/Base_Dao.php');
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/21
 * Time: 20:15
 */
class Record_m extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Chat_m');
    }

    public function add($record)
    {
        $table_name = 'record';
        return Base_Dao::insert($table_name, $record);
    }

    private function update_lent_count($order_id)
    {
        $sql = "UPDATE share_items, record"
            . " SET lend_count = lend_count + 1"
            . " WHERE share_items.id = record.card_id"
            . " AND record.id = ?"
            . " AND record.type = 1";
        $param = array($order_id);
        return Base_Dao::update_by_sql($sql, $param);
    }

    public function get_order($open_id, $longitude, $latitude, $page_num, $page_size) {
        $offset = ($page_num - 1) * $page_size;
        $sql = "SELECT R.id, S.shop_name, S.img AS shop_img, S.shop_location,"
            . " S.shop_longitude, S.shop_latitude, S.discount, S.trade_type,"
            . " R.status, R.borrow_id, UB.nickname AS borrow_name, UB.gender AS borrow_gender,"
            . " UB.avatar AS borrow_gender, R.lend_id, UL.nickname AS lend_name,"
            . " UL.gender AS lend_gender, UL.avatar AS lend_avatar,"
            . " R.t_apply, R.t_agree, R.t_return, R.t_pay, R.t_ver_pay, R.t_cancel, R.type AS card_type,"
            . " C.content AS last_chat, C.time AS last_chat_time"
            . " FROM record AS R, share_items AS S, users UB, users UL, chat C"
            . " WHERE R.card_id = S.id AND R.borrow_id = UB.open_id AND R.lend_id = UL.open_id"
            . " AND (R.borrow_id = ? OR R.lend_id = ?) AND R.id = C.order_id"
            . " GROUP BY R.id ORDER BY t_create DESC, C.time DESC LIMIT ?, ?";
        $param = array($open_id, $open_id, (int) $offset, (int) $page_size);
        $data =  Base_Dao::query_by_sql($sql, $param);
        return $this->order_processing($data, $open_id, $longitude, $latitude);
    }

    public function order_processing($data, $open_id, $longitude, $latitude) {
        $result = array();
        foreach($data as $item) {
            $item['shop_img'] = json_decode($item['shop_img']);
            array_push($result, $item);
        }

        return $result;
    }

    private function set_result_of_get(& $record, $paras)
    {
        $this->set_user_info($record);// 获取借主和卡主的信息
        $record['shop_img'] = json_decode($record['shop_img']); // 将商店图片转化为json格式

        if ($paras['longitude'] != null && $paras['latitude'] != null) { // 获取店的距离和卡的距离
            $record['shop_distance'] = Distance_Util::get_kilometers_between_points($paras['latitude'], $paras['longitude'], $record['shop_latitude'], $record['shop_longitude']);
            $record['lend_distance'] = Distance_Util::get_kilometers_between_points($paras['latitude'], $paras['longitude'], $record['owner_latitude'], $record['owner_longitude']);

            $record['shop_distance'] = round($record['shop_distance'], 1); // 四舍五入, 保留1位小数
            $record['lend_distance'] = round($record['lend_distance'], 1);
        }

        $record['shop_name'] = substr($record['shop_name'], 0, 60); // 截取店名的前20位

        $this->unset_location($record); // 撤销位置信息
    }

    /**
     * 设置用户信息
     * @param $record
     */
    private function set_user_info(& $record)
    {
        $borrow_id = $record['borrow_id'];
        $lend_id = $record['lend_id'];

        if (($borrow_user = $this->query_user_by_id($borrow_id)) != false) {
            $record['borrow_name'] = $borrow_user->nickname;
            $record['borrow_avatar'] = $borrow_user->avatar;
        }

        if (($lend_user = $this->query_user_by_id($lend_id)) != false) {
            $record['lend_name'] = $lend_user->nickname;
            $record['lend_avatar'] = $lend_user->avatar;
        }
    }

    /**
     * 撤去店的位置信息和卡主的位置信息
     * @param $record
     */
    private function unset_location(& $record)
    {
        unset($record['shop_longitude']);
        unset($record['shop_latitude']);
        unset($record['owner_longitude']);
        unset($record['owner_latitude']);
    }

    public function get_by_id($id)
    {
        if (($type = $this->get_type_by_id($id)) == 1) {
            $select_sql = " SELECT R.id, R.borrow_id, R.lend_id, R.status, R.t_apply, R.t_cancel, R.t_agree, R.t_return, R.t_pay, R.t_ver_pay,"
                . " S.shop_name, S.ware_type, S.trade_type, S.discount, S.img AS shop_img"
                . " FROM record AS R JOIN share_items AS S ON R.card_id = S.id"
                . " WHERE R.id = $id";
        } elseif ($type == 2) {
            $select_sql = " SELECT R.id, R.borrow_id, R.lend_id, R.status, R.t_apply, R.t_cancel, R.t_agree, R.t_return, R.t_pay, R.t_ver_pay,"
                . " C.shop_name, C.ware_type, C.trade_type, C.discount"
                . " FROM record AS R JOIN request_card AS C ON R.card_id = C.id"
                . " WHERE R.id = $id";
        }

        $result = array();

        try {
            $this->load->database();
            Log_Util::log_sql($select_sql, __CLASS__);
            $query = $this->db->query($select_sql);
            $this->db->close();
            $result = $query->row_array();
            if ($type == 1)
                $result['shop_img'] = json_decode($result['shop_img']); // 将多张图片转为json信息
            $this->set_user_info($result);
            return $result;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    private function get_type_by_id($id)
    {
        $select_sql = " SELECT type"
            . " FROM record"
            . " WHERE id = $id";

        try {
            $this->load->database();
            $query = $this->db->query($select_sql);
            $this->db->close();
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                return $row['type'];
            }
        } catch (Exception $e) {
            $this->db->close();
        }
    }

    public function query_records($borrow_id, $lend_id, $type)
    {
        $records = array();
        try {
            $sql = 'SELECT record.id, record.borrow_id, users.borrow_name, record.lend_id, record.lend_name,'
                . ' record.card_id, record.status, record.cancel_time, record.apply_time,'
                . ' record.return_time, record.lend_time, record.agree_time, record.reject_time'
                . ' share_items.shop_name, share_items.ware_type, share_items.discount,'
                . ' share_items.trade_type, share_items.shop_location, share_items.shop_longitude,'
                . ' share_items.shop_latitude, share_items.img, share_items.share_type'
                . ' FROM record, share_items'
                . ' WHERE share_items.id = record.card_id';

            if ($borrow_id != null) {
                $sql = $sql . ' AND record.borrow_id = ' . $borrow_id;
            }
            if ($lend_id != null) {
                $sql = $sql . ' AND record.lend_id = ' . $lend_id;
            }

            switch ($type) {
                case 3:
                    $sql = $sql . ' , cancel_time != NULL';
                    break;
                case 2:
                    $sql = $sql . ' , apply_time != NULL';
                    break;
                case 1:
                    $sql = $sql . ' , return_time != NULL';
                    break;
                case -1:
                    $sql = $sql . ' , lend_time != NULL';
                    break;
                case -2:
                    $sql = $sql . ' , agree_time != NULL';
                    break;
                case -3:
                    $sql = $sql . ' , reject_time != NULL';
                    break;
            }

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();
            foreach ($query->result_array() as $item) {
                array_push($records, $item);
            }
        } catch (Exception $e) {
            $this->db->close();
            $records = array();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $records;
    }

//    /**
//     * @param $card_id 卡的id
//     * @param $status 3=取消申请，2=申请借卡,1=归还,-1=借出，-2=同意借卡，-3=拒绝借卡
//     * @return bool
//     */
//    public function update($record)
//    {
//        $result = true;
//        try {
//            $sql = 'UPDATE share_items SET status = ' . $record['status'];
//            switch ($record['status']) {
//                case 3:
//                    $sql = $sql . ' , cancel_time = ' . $record['cancel_time'];
//                    break;
//                case 2:
//                    $sql = $sql . ' , apply_time = ' . $record['apply_time'];
//                    break;
//                case 1:
//                    $sql = $sql . ' , return_time = ' . $record['return_time'];
//                    break;
//                case -1:
//                    $sql = $sql . ' , lend_time = ' . $record['lend_time'];
//                    break;
//                case -2:
//                    $sql = $sql . ' , agree_time = ' . $record['agree_time'];
//                    break;
//                case -3:
//                    $sql = $sql . ' , reject_time = ' . $record['reject_time'];
//                    break;
//            }
//            $sql = $sql . 'WHERE id = ' . $record['id'];
//
//            Log_Util::log_sql($sql, __CLASS__);
//
//            $this->load->database();
//            $this->db->query($sql);
//            $this->db->close();
//        } catch (Exception $e) {
//            $result = false;
//            Log_Util::log_sql($e->getMessage(), __CLASS__);
//        }
//
//        return $result;
//    }

    /**
     * 更新该借卡记录的状态, 并更改时间
     * @param $record
     * @return bool
     */
    public function update($id, $card_id, $card_type, $status, $time)
    {
        $sql0 = $this->get_update_sql($status);
        $param0 = array($status, $time, $id);
        $sql1 = "UPDATE share_items SET lend_count = lend_count + 1 WHERE id = ?";
        $param1 = array($card_id);
        if($status == 3 && $card_type == 1) {
            $sql_array = array($sql0, $sql1);
            $param_array = array($param0, $param1);
            $result = Base_Dao::query_for_trans($sql_array, $param_array);
        } else {
            $result = Base_Dao::update_by_sql($sql0, $param0);
        }

        return $result;
    }

    public function get_update_sql($status) {
        $sql = "UPDATE record SET status = ?";
        switch ($status) {
            case 1:
                $sql = $sql . ", t_agree = ?";
                break;
            case 2:
                $sql = $sql . ", t_return = ?";
                break;
            case 3:
                $sql = $sql . ", t_pay = ?";
                break;
            case 4:
                $sql = $sql . ", t_ver_pay = ?";
                break;
        }
        $sql = $sql . " WHERE id = ?";

        return $sql;
    }

    /**
     * 通过id判断该借卡记录是否存在
     * @param $id
     * @return bool
     */
    public function query_by_id($id)
    {
        $sql = "SELECT * FROM record WHERE id = ?";
        $param = array($id);
        return Base_Dao::query_one_by_sql($sql, $param);
    }

    public function query_record($card_id, $borrow_id, $lend_id, $type)
    {
        $sql = ' SELECT id FROM record WHERE card_id = ? AND borrow_id = ?'
            . ' AND lend_id = ? AND type = ? AND status != 4';
        $param = array($card_id, $borrow_id, $lend_id, $type);
        return Base_Dao::query_one_by_sql($sql, $param);
    }

    public function query_card_record($card_id, $borrow_id, $lend_id) {
        $sql = " SELECT s.owner, s.shop_name, s.ware_type, s.discount, s.trade_type, s.shop_location, s.img,"
            . " s.rating_average, s.rating_num, s.lend_count, b.open_id AS borrow_open_id, b.nickname AS borrow_nickname,"
            . " b.gender AS borrow_gender, b.avatar AS borrow_avatar, l.open_id AS lend_open_id, l.nickname AS lend_nickname,"
            . " l.gender AS lend_gender, l.avatar AS lend_avatar, r.id, r.card_id, r.type, r.status, r.t_agree, r.t_return, r.t_pay, r.t_ver_pay"
            . " FROM record as r, share_items as s, users as b, users as l"
            . " WHERE r.card_id = ? AND r.card_id = s.id AND r.borrow_id = ? AND r.lend_id = ?"
            . " AND b.open_id = r.borrow_id AND l.open_id = r.lend_id "
            . " AND status != 4 AND type = 1";
        $param = array($card_id, $borrow_id, $lend_id);
        $result = Base_Dao::query_one_by_sql($sql, $param);
        if($result != null) {
            $result['img'] = json_decode($result['img']);
        }
        return $result;
    }

    public function query_request_record($request_id, $borrow_id, $lend_id)
    {
        $sql = " SELECT rc.open_id, rc.shop_name, rc.ware_type, rc.discount, rc.trade_type, rc.shop_location,"
            . " b.open_id AS borrow_open_id, b.nickname AS borrow_nickname,"
            . " b.gender AS borrow_gender, b.avatar AS borrow_avatar, l.open_id AS lend_open_id, l.nickname AS lend_nickname,"
            . " l.gender AS lend_gender, l.avatar AS lend_avatar, r.id, r.card_id, r.type, r.status, r.t_agree, r.t_return, r.t_pay, r.t_ver_pay"
            . " FROM record as r, request_card as rc, users as b, users as l"
            . " WHERE r.card_id = ? AND r.card_id = rc.id AND r.borrow_id = ? AND r.lend_id = ?"
            . " AND b.open_id = r.borrow_id AND l.open_id = r.lend_id "
            . " AND status != 4 AND type = 2";
        $param = array($request_id, $borrow_id, $lend_id);
        $result = Base_Dao::query_one_by_sql($sql, $param);
        if($result != null) {
            $result['img'] = json_decode($result['img']);
        }
        return $result;
    }
}