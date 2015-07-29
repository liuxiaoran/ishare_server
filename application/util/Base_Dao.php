<?php
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/7/21
 * Time: 14:42
 */

class Base_Dao {

    public function insert($table_name, $param) {
        $id = 0;
        try {
            $this->load->database();
            $this->db->insert($table_name, $param);
            $id = $this->db->insert_id();
            $this->db->close();
        } catch (Exception $e) {
            $id = 0;
            $this->db->close();
        }

        return $id;
    }

    public function delete($table_name, $param) {
        $result = false;
        try {
            $this->load->database();
            $this->db->delete($table_name, $param);
            $this->db->close();
            $result = true;
        } catch (Exception $e) {
            $result = false;
            $this->db->close();
        }
        return $result;
    }

    public function update($table_name, $update, $where) {
        $result = false;
        try {
            $this->load->database();
            $this->db->update($table_name, $update, $where);
            $this->db->close();
            $result = true;
        } catch (Exception $e) {
            $this->db->close();
            $result = false;
        }
        return $result;
    }

    public function query($table_name, $select, $where) {
        $result = array();
        try {
            $this->load->database();
            $this->db->select($select);
            foreach($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $this->db->from($table_name);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    array_push($result, $row);
                }
            }
            $this->db->close();
        } catch (Exception $e) {
            $result = array();
            $this->db->close();
        }
        return $result;
    }

    public function query_by_id($table_name, $select, $where) {
        $result = null;
        try {
            $this->load->database();
            $this->db->select($select);
            foreach($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $this->db->from($table_name);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result = $query->row_array();
            }
            $this->db->close();
        } catch (Exception $e) {
            $result = null;
            $this->db->close();
        }
        return $result;
    }

    public function query_by_sql($sql, $param) {
        $result = array();
        try {
            $this->load->database();
            $query = $this->db->query($sql, $param);
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    array_push($result, $row);
                }
            }
        } catch (Exception $e) {
            $result = array();
            $this->db->close();
        }

        return $result;
    }

    public function query_one_by_sql($sql, $param) {
        $result = null;
        try {
            $this->load->database();
            $query = $this->db->query($sql, $param);
            if ($query->num_rows() > 0) {
                $result = $query->row_array();
            }
        } catch (Exception $e) {
            $result = array();
            $this->db->close();
        }

        return $result;
    }

    public function update_by_sql($sql, $param) {
        $result = false;
        try {
            $this->load->database();
            $this->db->query($sql, $param);
            $this->db->close();
            $result = true;
        } catch (Exception $e) {
            $this->db->close();
            $result = false;
        }
        return $result;
    }

    public function query_for_trans($sql_array, $params_array) {
        $status = false;
        try {
            $this->load->database();
            $this->db->trans_start();
            for($i = 0; $i < count($sql_array); $i++) {
                $this->db->query($sql_array[$i], $params_array[$i]);
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            $status = false;
        }
        return $status;
    }

    public function delete_for_trans($sql, $params) {
        $status = false;
        try {
            $this->load->database();
            $this->db->trans_start();
            for($i = 0; $i < count($params); $i++) {
                $this->db->query($sql, $params[$i]);
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $this->db->close();
            $status = false;
        }
        return $status;
    }
}