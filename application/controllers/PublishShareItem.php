<?php
/**
 * Created by PhpStorm.
 * User: galaxy
 * Date: 15/4/29
 * Time: 下午4:42
 */
class PublishShareItem extends CI_Controller{
    public  function index (){

        $phone = $_GET["phone"];
        $key = $_GET["key"];

        $this->load->model('verify_session');
        if(!$this->verify_session->verify($phone, $key)) {
            $ret = array('r'=>'2', 'v'=>"not login");
            echo json_encode($ret);
        }

        $owner = array_key_exists("owner", $_POST) ? $_POST["owner"] : null;
        $type = array_key_exists("type", $_POST) ? $_POST["type"] : null;
        $title = array_key_exists("title", $_POST) ? $_POST["title"] : null;
        $description = array_key_exists("description", $_POST) ? $_POST["description"] : null;
        $img = array_key_exists("img", $_POST) ? $_POST["img"] : null;
        $province = array_key_exists("province", $_POST) ? $_POST["province"] : null;
        $city = array_key_exists("city", $_POST) ? $_POST["city"] : null;
        $location = array_key_exists("location", $_POST) ? $_POST["location"] : null;
        $share_type = array_key_exists("share_type", $_POST) ? $_POST["share_type"] : null;

        $this->load->database();
        $sql = "insert into share_items(owner, type, title, description, img, province, city, location, share_type) values('$owner', '$type', '$title', '$description', '$img', '$province', '$city', '$location', '$share_type')";
        $query = $this->db->query($sql);

        $retId = $this->db->insert_id();

        $this->shareToFriend($phone, $retId);
        $this->db->close();

        $ret =  array ("r"=>0, "v"=> "success");
    }

    private function shareToFriend($phone, $shareId) {
        $sql = "select contact_phone from contacts where host_phone='$phone'";
        $query = $this->db->query($sql);
        if($query->num_rows() > 0) {
            foreach($query->result() as $val) {
                $sql = "select discovery_list, list_length from user_discovery where phone='$val->contact_phone'";
                $query = $this->db->query($sql);
                if($query->num_rows() === 1) {
                    $result = $query->row();

                    $discoveryList = $result->discovery_list;
                    $listLength = $result->list_length;

                    $listLength++;
                    $discoveryList = $discoveryList.":".$shareId;

                    if($listLength > 200) {
                        $listLength = 200;
                        echo "pos : ".strpos($discoveryList, ':');
                        $discoveryList = substr($discoveryList, strpos($discoveryList, ':') + 1);
                    }

                    $sql = "update user_discovery set discovery_list='$discoveryList', list_length='$listLength' where phone='$val->contact_phone'";
                    $this->db->query($sql);
                }
            }
        }

    }

    private function shareToFriend2($phone, $shareId) {

    }

}