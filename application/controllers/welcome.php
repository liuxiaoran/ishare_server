<?php
/**
 * Created by IntelliJ IDEA.
 * User: primary
 * Date: 15/7/14
 * Time: 下午2:35
 */

class welcome extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    function index(){

        $this->load->helper('url');

        $this->load->view('welcome_message');
    }

}