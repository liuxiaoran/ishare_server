<?php
/**
 * Created by IntelliJ IDEA.
 * User: primary
 * Date: 15/7/14
 * Time: 下午2:35
 */

class customerservice extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('chat_m');
    }

    function index(){

        $this->load->helper('url');

        //$data['contact_list'] = $this->chat_m->query_customer(10,"oyIsQtw-b6cNXbEbODDHKBq1SXcw");

        $data['page_title'] = 'Your title';
        $this->load->view('chat_message',$data);
    }

}