<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/25
 * Time: 11:57
 */
class Get_Token_C extends CI_Controller
{
    private $access_key;
    private $secret_key;
    private $bucket_name;
    private $verify_Util;

    public function __construct($access_key = 'Dfd-oTYu60HK8l9YB0mH3H2gaoDHIswiyc7vW04M',
                                $secret_key = 'kaKDYivGveyZzvyGh3LaR3rT9dM0gYG2hBc5YqgS',
                                $bucket_name = 'ishare')
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->bucket_name = $bucket_name;
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if ($this->User_m->verify_session_key($_GET)) {
            $qiniu_key = $this->get_data();
            $message = $this->check_data($qiniu_key);
            if ($message == null) {
                $data = $this->sign_with_data($this->get_put_policy($qiniu_key));//$this->qiniu_token($put_policy);//
                $ret['token'] = $data;
                $ret['status'] = 0;
                $ret['message'] = 'exe sql success';
            } else {
                $ret['status'] = -1;
                $ret['message'] = $message;
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        }

        echo json_encode($ret);
    }

    public function get_data()
    {
        return array_key_exists("qiniu_key", $_GET) ? $_GET["qiniu_key"] : null;
    }

    public function get_put_policy($qiniu_key, $expires = 3600)
    {
        $data['deadline'] = time() + $expires;
        $data['scope'] = $this->bucket_name . ':' . $qiniu_key;

        return json_encode($data);
    }

    public function check_data($put_policy)
    {
        $message = null;
        if ($put_policy == null) {
            $message = 'put_policy不能为空';
        }
        return $message;
    }

    public function qiniu_encode($str) // URLSafeBase64Encode
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }

    public function sign($data)
    {
        $hmac = hash_hmac('sha1', $data, $this->secret_key, true);
//        echo '$hmac :' . $this->qiniu_encode($hmac) . "\n";
        return $this->access_key . ':' . $this->qiniu_encode($hmac);
    }

    public function sign_with_data($data)
    {
        $data = $this->qiniu_encode($data);
//        echo '$data :' . $data . "\n";
        return $this->sign($data) . ':' . $data;
    }

}