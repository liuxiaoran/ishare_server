<?php
/**
 * Created by PhpStorm.
 * User: galaxy
 * Date: 15/4/27
 * Time: 下午2:39
 */
 class VeritySession extends CI_Model {
     public function verify($id, $key) {
         $this->load->database();
         $query = $this->db->query("select from users where id='$id' and session_key='$key'");
         if($query->num_rows() === 1) {
             return true;
         } else {
             return false;
         }
         $this->db->close();
    }
 }

?>