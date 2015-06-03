<?php

class Request_m extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($paras)
    {
        try {
            $this->load->database();
            $this->db->insert('request_card', $paras);
            $this->db->close();
            return true;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    public function get($paras)
    {

    }
}