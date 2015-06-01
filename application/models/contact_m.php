<?php
require_once(dirname(__FILE__) . '/../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/11
 * Time: 10:04
 */
class Contact_m extends CI_Model
{

    public function add_contact($host_phone, $contactList)
    {
        $status = true;
        try {
            $this->load->database();
            $this->db->trans_start();
            foreach ($contactList as $contact) {
                $query = 'select * from contacts where host_phone=? and contact_phone=?';
                $result = $this->db->query($query, array($host_phone, $contact['phone']));
                if ($result->num_rows() === 1) {
                    $query = 'insert into contacts (host_phone, contact_phone, contact_name) values(?, ?, ?)';
                    $this->db->query($query, array($host_phone, $contact['phone'], $contact['name']));
                }
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }
        return $status;
    }

    public function delete_contact($host_phone, $contactList)
    {
        $status = true;
        try {
            $this->load->database();
            $this->db->trans_start();
            foreach ($contactList as $contact) {
                $sql = 'select * from contacts where host_phone=? and contact_phone=?';
                $query = $this->db->query($sql, array($host_phone, $contact['phone']));
                if ($query->num_rows() === 1) {
                    $sql = 'delete from contacts where host_phone=? and contact_phone=?)';
                    $this->db->query($sql, array($host_phone, $contact['phone']));
                }
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $status;
    }

    public function update_contact($host_phone, $contact_list)
    {
        $status = true;
        try {
            $sql = "DELETE FROM contacts WHERE host_phone = ?";
            $this->load->database();
            $this->db->trans_start();
            $this->db->query($sql, array($host_phone));
            foreach ($contact_list as $contact) {
                $sql = 'insert into contacts (host_phone, contact_phone, contact_name) values(?, ?, ?)';
                $this->db->query($sql, array($host_phone, $contact['phone'], $contact['name']));
            }
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            $this->db->close();
        } catch (Exception $e) {
            $status = false;
            $this->db->close();
            Log_Util::log_sql($e->getMessage(), __CLASS__);
        }

        return $status;
    }
}