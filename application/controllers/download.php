<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/26
 * Time: 21:35
 */
class download extends CI_Controller
{

    private $root_dir = '/data/wwwroot/apk/';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['file'] = array_key_exists('file', $_GET) ? $_GET["file"] : null;
        if ($data['file'] == null) {
            echo '请填写下载文件名';
        }
        $data['file'] = $this->root_dir . $data['file'];
        $this->load->view('download', $data);
    }
}