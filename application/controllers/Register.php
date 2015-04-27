<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	public function index() {
		if(array_key_exists('id', $_GET) && array_key_exists('pw', $_GET)) {
            $id=$_GET['id'];
            $pw=$_GET['pw'];
        } else {
            $ret = array('r'=>1, 'v'=>'null param');
            echo json_encode($ret);
            exit;
        }

        $this->load->database();
        $sql = sprintf("select * from users where id='%s'", $id);
        $query = $this->db->query($sql);
        if(count($query->result()) === 0) {
            $pw=md5($pw);
            $key = md5($id + time());
            $this->db->query("insert into users(id, pw, session_key) values ('$id', '$pw', '$key')");

            $ret = array('r'=>'0', 'v'=>'success', 'key'=>$key);
            echo json_encode($ret);
        } else {
            $ret = array('r'=>2, 'v'=>'exist');
            echo json_encode($ret);
        }
        $this->db->close();
	}
}

?>

