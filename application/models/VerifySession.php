<?php
/**
 * Created by PhpStorm.
 * User: galaxy
 * Date: 15/4/27
 * Time: 下午2:39
 */
 class VerifySession extends CI_Model {
     public function verify($phone, $key) {
         $this->load->database();
         $query = $this->db->query("select phone from users where phone='$phone' and session_key='$key'");
         if($query->num_rows() === 1) {
             return true;
         } else {
             return false;
         }
         $this->db->close();
    }
 }

?>