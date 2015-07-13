<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/23
 * Time: 12:03
 */
class location_m extends CI_Model
{

    public function add($location)
    {
        $id = 0;
        try {
            $this->load->database();
            $this->db->insert('location', $location);
            $id = $this->db->insert_id();
            $this->db->close();
        } catch (Exception $e) {
            $id = 0;
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
            $this->db->close();
        }

        return $id;
    }

    public function delete($id)
    {
        try {
            $this->load->database();
            $this->db->delete('location', array('id' => $id));
            $this->db->close();
            return true;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    public function update($paras, $id)
    {
        try {
            $this->load->database();
            $this->db->update('location', $paras, array('id' => $id));
            $this->db->close();
            return true;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    public function get($open_id)
    {
        $locations = array();
        try {
            $this->load->database();
            $sql = "SELECT * FROM location  WHERE open_id = '$open_id'";
            Log_Util::log_sql($sql, __CLASS__);
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    array_push($locations, $row);
                }
            }
            $this->db->close();
        } catch (Exception $e) {
            $locations = array();
            Log_Util::log_sql_exc($e->getMessage(), __CLASS__);
            $this->db->close();
        }
        return $locations;
    }
}