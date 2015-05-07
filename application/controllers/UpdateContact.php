<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UpdateContact extends CI_Controller
{

    public function index()
    {


        $contactStr = $_POST['upload_contact'];
        $jsonObject = json_encode($contactStr, true);

        $host_phone = $jsonObject["user_phone"];

        $contactList = $jsonObject["contacts"];

        foreach ($contactList as $contact) {
            $data[0] = $host_phone;
            $data[1] = $contact["phone"];
            $query = $this->db->query("select * from contacts where host_phone=? and contact_phone=?", $data);
            if ($query->num_rows() === 0) {
                $this->db->query("insert into contacts (host_phone, contact_phone, contact_name) values(?, ?,?)", array($data[0], $data[1], $contact["name"]));
            }
        }

        $this->db->close();

        $ret = array('status' => '0', 'message' => "success");
        echo json_encode($ret);


//        $phone = $_GET['phone'];
//        $key = $_GET['key'];
//
//        $this->load->model('verify_session');
//        if(!$this->verify_session->verify($phone, $key)) {
//            $ret = array('r'=>'2', 'v'=>"not login");
//            echo json_encode($ret);
//        }
//
//        $this->load->database();
//        //TESTURL = http://localhost/ishare_server/index.php/updatecontact?phone=18500138088&key=123456&data={%22del%22:[{%22phone%22:13810459534},{%22phone%22:13810459533}],%22add%22:[{%22phone%22:18500138088}]}
//
//        if(array_key_exists('add', $_POST)) {
//            $addStr = $_POST['add'];
//
//            $addList = json_decode($addStr);
//            foreach ($addList as $val) {
//                $contact = $val->phone;
//                if (!$contact) {
//                    continue;
//                }
//                $query = $this->db->query("select * from contacts where host_phone='$phone' and contact_phone='$contact'");
//                if ($query->num_rows() === 0) {
//                    $this->db->query("insert into contacts (host_phone, contact_phone) values('$phone', '$contact')");
//                }
//            }
//        }
//
//        if(array_key_exists('del', $_POST)) {
//            $delStr = $_POST['del'];
//
//            $delList = json_decode($delStr);
//            foreach ($delList as $val) {
//                $contact = $val->phone;
//                if (!$contact) {
//                    continue;
//                }
//                $this->db->query("delete from contacts where host_phone='$phone' and contact_phone='$contact'");
//            }
//
//        }
//
//        $this->db->close();
//
//        $ret = array('r'=>'0', 'v'=>"success");
//        echo json_encode($ret);
    }
}

?>