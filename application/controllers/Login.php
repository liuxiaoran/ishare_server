<?php

/**
 * Created by PhpStorm.
 * User: liuxiaoran
 * Date: 15/4/27
 * Time: 下午4:42
 */
class Login extends CI_Controller
{
    public function index()
    {

        if (array_key_exists("phone", $_POST) && array_key_exists("password", $_POST)) {

            $data[0] = $_POST["phone"];
            $data[1] = $_POST["password"];

            $this->load->database();

            $sql = "select * from users where phone=? and pw = ?";
            $result = $this->db->query($sql, $data);

            if (count($result) === 0) {
                $ret = array("status" => -1, "message" => "password error");
            } else {
                $key = md5($data[0] + time());
                $ret = array("status" => 0, "message" => "success", "key" => $key);
                $sql = "update users set session_key =? where phone=?";
                $this->db->query($sql, array($key, $data[0]));


            }


        } else {

            $ret = array("status" => -1, "message" => "info lose");
        }


        echo json_encode($ret);
        $this->db->close();
    }


}