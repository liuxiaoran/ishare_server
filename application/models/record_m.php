<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/21
 * Time: 20:15
 */
class Record_m extends CI_Model
{

    public function add($record)
    {
        $status = true;
        try {
            $this->db->insert('record', $record);
        } catch (Exception $e) {
            $status = false;
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $status;
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
            foreach ($query->result_array() as $item) {
                array_push($records, $item);
            }
        } catch (Exception $e) {
            $records = array();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $records;
    }

    /**
     * @param $card_id 卡的id
     * @param $status 3=取消申请，2=申请借卡,1=归还,-1=借出，-2=同意借卡，-3=拒绝借卡
     * @return bool
     */
    public function update($record)
    {
        $result = true;
        try {
            $sql = 'UPDATE share_items SET status = ' . $record['status'];
            switch ($record['status']) {
                case 3:
                    $sql = $sql . ' , cancel_time = ' . $record['cancel_time'];
                    break;
                case 2:
                    $sql = $sql . ' , apply_time = ' . $record['apply_time'];
                    break;
                case 1:
                    $sql = $sql . ' , return_time = ' . $record['return_time'];
                    break;
                case -1:
                    $sql = $sql . ' , lend_time = ' . $record['lend_time'];
                    break;
                case -2:
                    $sql = $sql . ' , agree_time = ' . $record['agree_time'];
                    break;
                case -3:
                    $sql = $sql . ' , reject_time = ' . $record['reject_time'];
                    break;
            }
            $sql = $sql . 'WHERE id = ' . $record['id'];

            Log_Util::log_sql($sql, __CLASS__);

            $this->load->database();
            $this->db->query($sql);
            $this->db->close();
        } catch (Exception $e) {
            $result = false;
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $result;
    }

}