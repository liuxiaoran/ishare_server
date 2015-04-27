<?php
/**
 * Created by PhpStorm.
 * User: liuxiaoran
 * Date: 15/4/27
 * Time: 下午4:42
 */
class login extends CI_Controller{
    public  function index (){



        if (array_key_exists("phone",$_POST) && array_key_exists("password", $_POST)){

            $phone = $_POST["phone"];
            $password = $_POST["password"];


            $this->load->database();
            $sql = sprintf("select * from users where phone='%s'", $phone);
            $query = $this->db->query($sql);
        }else {

            $ret =  array ("status"=>-1, "message"=> "info lose");
        }



    }


}