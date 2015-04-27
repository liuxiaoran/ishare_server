<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UpdateContact extends CI_Controller
{

    public function index()
    {
        $phone = $_GET['phone'];
        $key = $_GET['key'];

        $this->load->model("VerifySession");
        if(!$this->VerifySession->verify($phone, $key)) {
            $ret = array('r'=>'2', 'v'=>"not login");
            echo json_encode($ret);
        }

        $this->load->database();
        //$contactStr = "13800000001;13800000002;13800000003;13800000004;13800000005;13800000006;13800000007;13800000008";
        if(array_key_exists('add', $_POST)) {
            $addContactStr = $_POST['add'];
            $addContactArray = explode(";", $addContactStr);
            print_r($addContactArray);
            echo "<br>";

            foreach($addContactArray as $val) {
                $query = $this->db->query("select * from contacts where host_phone='$phone' and contact_phone='$val'");
                if($query->num_rows() === 0) {
                    $this->db->query("insert into contacts (host_phone, contact_phone) values('$phone', '$val')");
                }
            }
        }

        if(array_key_exists('del', $_POST)) {
            $delContactStr = $_POST['del'];
            $delContactArray = explode(";", $delContactStr);
            print_r($delContactArray);
            echo "<br>";

            foreach($delContactArray as $val) {
                $this->db->query("delete from contacts where host_phone='$phone' and contact_phone='$val'");
            }
        }

        $this->db->close();
    }
}
?>