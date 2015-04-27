<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	public function index() {
		if(array_key_exists('phone', $_GET) && array_key_exists('pw', $_GET)) {
            $phone=$_GET['phone'];
            $pw=$_GET['pw'];
        } else {
            $ret = array('status'=>1, 'message'=>'null param');
            echo json_encode($ret);
            exit;
        }

        $this->load->database();
        $sql = sprintf("select * from users where phone='%s'", $phone);
        $query = $this->db->query($sql);
        if(count($query->result()) === 0) {
            $pw=md5($pw);
            $key = md5($phone + time());
            $this->db->query("insert into users(phone, pw, session_key) values ('$phone', '$pw', '$key')");

            $ret = array('status'=>'0', 'message'=>'success', 'key'=>$key);
            echo json_encode($ret);
        } else {
            $ret = array('status'=>2, 'message'=>'exist');
            echo json_encode($ret);
        }
        $this->db->close();
	}
}

?>

