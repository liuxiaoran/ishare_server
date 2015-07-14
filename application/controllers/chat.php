<?php
/**
 * Created by IntelliJ IDEA.
 * User: primary
 * Date: 15/7/14
 * Time: 下午2:35
 */

class chat extends CI_Controller {
    public function _construct(){
        parent::__construct();
        $this->load->model('chat_m');
        $this->load->view('chat_message');
    }

}