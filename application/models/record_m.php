<?php
require_once(dirname(__FILE__) . '/../util/Distance_Util.php');

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
        try {
            $this->load->database();
            $this->db->insert('record', $record);
            $id = $this->db->insert_id();
            $this->db->close();
        } catch (Exception $e) {
            $id = false;
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $id;
    }

    private function update_lent_count($order_id)
    {
        $update_sql = " UPDATE share_items, record"
            . " SET lend_count = lend_count + 1"
            . " WHERE share_items.id = record.card_id"
            . " AND record.id = $order_id"
            . " AND record.type = 1";
        try {
            $this->db->query($update_sql);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function get($paras)
    {
        $records = array();
        $borrow_id = $paras["borrow_id"];
        $lend_id = $paras["lend_id"];
        $sql = "SELECT R.id, S.shop_name, S.img AS shop_img, S.shop_location, S.shop_longitude, S.shop_latitude, S.discount, S.trade_type,"
            . " R.status, R.borrow_id, R.lend_id, O.longitude AS owner_longitude, O.latitude AS owner_latitude,"
            . " R.t_apply, R.t_agree, R.t_return, R.t_pay, R.t_ver_pay, R.t_cancel"
            . " FROM ((record AS R JOIN share_items AS S ON R.card_id = S.id) JOIN owner_location AS O ON S.id = O.item_id)";

        // 判断是获取借入卡记录还是借出卡记录
        if ($paras['borrow_id'] != null) {
            $sql = $sql . " WHERE R.borrow_id = '$borrow_id'";
        } else {
            $sql = $sql . " WHERE R.lend_id = '$lend_id'";
        }

        $offset = ($paras['page_num'] - 1) * $paras['page_size'];
        $page_size = $paras['page_size'];

        $sql = $sql . " ORDER BY t_create DESC" . " LIMIT $offset, $page_size";

        try {
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();

            if ($query->num_rows() > 0) {
                foreach($query->result_array() as $record)
                {
                    $this->set_result_of_get($record, $paras);
                    array_push($records, $record);
                }
            }
        } catch (Exception $e) {
            $this->db->close();
        }

        return $records;
    }

    public function get_order($paras)
    {
        $records = array();
        $open_id = $paras["open_id"];
        $offset = ($paras['page_num'] - 1) * $paras['page_size'];
        $page_size = $paras['page_size'];

        $sql = "SELECT R.id, S.shop_name, S.img AS shop_img, S.shop_location, S.shop_longitude, S.shop_latitude, S.discount, S.trade_type,"
            . " R.status, R.borrow_id, R.lend_id, O.longitude AS owner_longitude, O.latitude AS owner_latitude,"
            . " R.t_apply, R.t_agree, R.t_return, R.t_pay, R.t_ver_pay, R.t_cancel"
            . " FROM ((record AS R JOIN share_items AS S ON R.card_id = S.id) JOIN owner_location AS O ON S.id = O.item_id)";

        $sql = $sql . " WHERE R.borrow_id = '$open_id' OR R.lend_id = '$open_id'";
        $sql = $sql . " ORDER BY t_create DESC" . " LIMIT $offset, $page_size";

        try {
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $record) {
                    $this->set_result_of_get($record, $paras);
                    $record['last_chat'] = $this->Chat_m->get_last_chat($record['id']);
                    array_push($records, $record);
                }
            }
        } catch (Exception $e) {
            $this->db->close();
        }

        return $records;
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

    /**
     * 通过id获取用户的资料
     * @param $id
     * @return mixed
     */
    private function query_user_by_id($id)
    {
        $sql = "SELECT * FROM users WHERE open_id = '$id'";

        try {
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();
            return $query->row();
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }

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
    public function update($record, $time)
    {
        $id = $record['id'];
        $status = $record['status'];


        try {
            $sql = "UPDATE record SET status = '$status'";
            $sql_time = "";
            switch ($status) {
                case 1:
                    $sql_time = ", t_agree = '$time'";
                    break;
                case 2:
                    $sql_time = ", t_return = '$time'";
                    break;
                case 3:
                    $sql_time = ", t_pay = '$time'";
                    $this->update_lent_count($id);
                    break;
                case 4:
                    $sql_time = ", t_ver_pay = '$time'";
                    break;
            }

            $sql = $sql . $sql_time . " WHERE id = '$id'";
            Log_Util::log_sql($sql, __CLASS__);
            $this->load->database();
            $this->db->query($sql);
            $this->db->close();
            $update_result = true;
        } catch (Exception $e) {
            $update_result = false;
            $this->db->close();
        }

        return $update_result;
    }

    /**
     * 通过id判断该借卡记录是否存在
     * @param $id
     * @return bool
     */
    public function is_exist($id)
    {
        $sql = "SELECT id FROM record WHERE id = '$id'";
        try {
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();
            return $query->num_rows() > 0 ? true : false;
        } catch (Exception $e) {
            $this->db->close();
        }

    }

    /**
     * 判断借卡记录中是否存在未完成的借卡记录
     * @param $card_id
     * @param $type
     * @param $borrow_id
     * @param $lend_id
     * @return mixed
     */
    public function is_exist_by_three_id($card_id, $type, $borrow_id, $lend_id)
    {
        $sql = " SELECT s.owner, s.shop_name, s.ware_type, s.discount, s.trade_type, s.shop_location, s.img,"
            . " s.rating_average, s.rating_num, s.lend_count, u1.open_id, u1.nickname, u1.gender, u1.avatar,"
            . " u2.open_id AS open_id1, u2.nickname AS nickname1, u2.gender AS gender1, u2.avatar AS avatar1,"
            . " r.id, r.status, t_agree, t_return, t_pay, t_ver_pay"
            . " FROM record as r, share_items as s, users u1, users as u2"
            . " WHERE s.id = '$card_id'"
            . " AND u1.open_id = '$borrow_id' "
            . " AND u2.open_id = '$lend_id'"
            . " AND r.card_id = '$card_id' "
            . " AND ((borrow_id = '$borrow_id' AND lend_id = '$lend_id') OR (borrow_id = '$lend_id' AND lend_id = '$borrow_id'))"
            . " AND (status = 0 OR status = 1 OR status = 2 OR status = 3)"
            . " AND type = " . $type;

        Log_Util::log_sql($sql, __CLASS__);

        try {
            $this->load->database();
            $query = $this->db->query($sql);
            $this->db->close();
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $row['img'] = json_decode($row['img']);
                return $row;
            }
            return false;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    public function query_card_with_three_id($card_id, $borrow_id, $lend_id)
    {
        try {
            $sql = "SELECT s.owner, s.shop_name, s.ware_type, s.discount, s.trade_type, s.shop_location, s.img,"
                . " s.rating_average, s.rating_num, s.lend_count, u1.open_id, u1.nickname, u1.gender, u1.avatar,"
                . " u2.open_id AS open_id1, u2.nickname AS nickname1, u2.gender AS gender1, u2.avatar AS avatar1"
                . " FROM share_items as s, users u1, users u2 WHERE s.id = '$card_id'"
                . " AND u1.open_id = '$borrow_id'"
                . " AND u2.open_id = '$lend_id'";

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $row['id'] = 0;
                $row['type'] = 1;
                $row['card_id'] = $card_id;
                $row['img'] = json_decode($row['img']);
                return $row;
            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            return null;
        }

    }

    public function query_request_with_three_id($request_id, $borrow_id, $lend_id)
    {
        try {
            $sql = "SELECT r.open_id as requester, r.shop_name, r.shop_location, r.discount, r.ware_type, r.trade_type, r.description,"
                . " u1.open_id, u1.nickname, u1.gender, u1.avatar,"
                . " u2.open_id AS open_id1, u2.nickname AS nickname1, u2.gender AS gender1, u2.avatar AS avatar1"
                . " FROM request_card as r, users u1, users u2"
                . " WHERE r.id = '$request_id'"
                . " AND u1.open_id = '$borrow_id'"
                . " AND u2.open_id = '$lend_id'";

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $row['id'] = 0;
                $row['type'] = 2;
                $row['card_id'] = $request_id;
                return $row;
            }
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            return null;
        }

    }
}