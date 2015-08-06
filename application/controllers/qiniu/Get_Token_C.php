<?php
require_once(dirname(__FILE__) . '/../../util/Log_Util.php');
require_once(dirname(__FILE__) . '/../../util/Param_Util.php');
require_once(dirname(__FILE__) . '/../../util/Ret_Factory.php');
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
    private $param_until;

    public function __construct($access_key = 'Dfd-oTYu60HK8l9YB0mH3H2gaoDHIswiyc7vW04M',
                                $secret_key = 'kaKDYivGveyZzvyGh3LaR3rT9dM0gYG2hBc5YqgS',
                                $bucket_name = 'ishare')
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->bucket_name = $bucket_name;
        $this->param_until = new Param_Util();
    }

    public function index()
    {
        Log_Util::log_param($_GET, __CLASS__);
        $param_names = array('qiniu_key');
        $param_need_names = array('open_id', 'key', 'qiniu_key');
        $params = $this->param_until->get_param($param_names, $_GET);
        $message = $this->param_until->check_param($_POST, $params, $param_need_names);

        if ($message != null) {
            $ret = Ret_Factory::create_ret(-1, $message);
        } else {
            if (!$this->User_m->verify_session_key($_POST)) {
                $ret = Ret_Factory::create_ret(2);
            } else {
                $data['token'] = $this->sign_with_data($this->get_put_policy($params['qiniu_key']));//$this->qiniu_token($put_policy);//
                $ret = Ret_Factory::create_ret(0, null, $data);
            }
        }

        echo json_encode($ret);
    }

    public function get_put_policy($qiniu_key, $expires = 3600)
    {
        $data['deadline'] = time() + $expires;
        $data['scope'] = $this->bucket_name . ':' . $qiniu_key;

        return json_encode($data);
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