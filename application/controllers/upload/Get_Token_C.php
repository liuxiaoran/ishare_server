<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/25
 * Time: 11:57
 */
class Get_Token_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);

        $ret = array();
        if ($this->verify()) {
            $put_policy = $this->get_data();
            $message = $this->check_data($put_policy);
            if ($message == null) {
                $data = $this->sign_with_data($put_policy);//$this->qiniu_token($put_policy);//
                $ret['data'] = $data;
                $ret['status'] = 0;
                $ret['message'] = 'exe sql success';
            } else {
                $ret['status'] = -1;
                $ret['message'] = 'exe sql error';
            }
        } else {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        }

        echo json_encode($ret);
    }

    public function get_data()
    {
        return array_key_exists("put_policy", $_GET) ? $_GET["put_policy"] : null;
    }

    public function check_data($put_policy)
    {
        $message = null;
        if ($put_policy == null) {
            $message = 'put_policy不能为空';
        }
        return $message;
    }

    public function verify()
    {
        $phone = array_key_exists("phone", $_GET) ? $_GET["phone"] : null;
        $key = array_key_exists("key", $_GET) ? $_GET["key"] : null;

        return $this->User_m->verify_session_key($phone, $key);
    }

    public function qiniu_encode($str) // URLSafeBase64Encode
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }

    public function qiniu_token($put_policy, $access_key = 'MY_ACCESS_KEY')
    {
        $encode_put_policy = $this->qiniu_encode($put_policy);
        echo '$encode_put_policy:' . $encode_put_policy . "\n";
        $sign = hash_hmac('sha1', $encode_put_policy, "<$access_key>", true);//hash_hmac('sha1', $encode_put_policy, $access_key, true);
        $encoded_sign = $this->qiniu_encode($sign);
        echo '$encoded_sign:' . $encoded_sign . "\n";
        $upload_token = $access_key . ':' . $encoded_sign . ':' . $encode_put_policy;
        echo '$upload_token:' . $upload_token . "\n";
        return $upload_token;
    }

    public function sign($data, $access_key = 'MY_ACCESS_KEY')
    {
        $hmac = hash_hmac('sha1', $data, $access_key, true);
        echo '$hmac :' . $this->qiniu_encode($hmac) . "\n";
        return $access_key . ':' . $this->qiniu_encode($hmac);
    }

    public function sign_with_data($data)
    {
        $data = $this->qiniu_encode($data);
        echo '$data :' . $data . "\n";
        return $this->sign($data) . ':' . $data;
    }

}