<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	public function index() {
		if(array_key_exists('id', $_GET) && array_key_exists('pw', $_GET)) {
		  $id=$_GET['id'];
  	  $pw=$_GET['pw'];
		} else {
			echo "1:param null";
			exit;	
		}
    
    $con = mysql_connect("localhost", "ishare", "123456");
    if(!$con){
        die('1' . mysql_error());
    } else {
        mysql_select_db("ishare", $con);
        $query = sprintf("select * from users where id='%s'", $id);
        $result = mysql_query($query);
        if(mysql_num_rows($result) === 0) {
        	$pw=md5($pw);
            mysql_query("insert into users values ('$id', '$pw')");
        } else {
            echo "1:exist";
        }
    }
    mysql_close($con);
    
    echo "<br><br>reg end";
	}
}

?>


