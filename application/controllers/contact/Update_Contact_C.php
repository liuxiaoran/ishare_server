<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/13
 * Time: 11:17
 */
class Update_Contact_C extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_m');
        $this->load->model('Contact_m');
    }

    public function index()
    {
        Log_Util::log_param($_POST, __CLASS__);

        $ret = array();
        if (!$this->User_m->verify_session_key($_GET)) {
            $ret['status'] = 2;
            $ret['message'] = 'not login';
        } else {
            $host_phone = array_key_exists("host_phone", $_POST) ? $_POST["host_phone"] : null;
            $contacts = array_key_exists("contacts", $_POST) ? $_POST["contacts"] : null;
            $contact_list = $contacts != null ? json_decode($contacts, true) : null;

            $message = $this->check_data($host_phone, $contact_list);

            if ($message != null) {
                $ret['status'] = -1;
                $ret['message'] = $message;
            } else {
                $status = $this->Contact_m->update_contact($host_phone, $contact_list);
                if ($status) {
                    $ret['status'] = 0;
                    $ret['message'] = 'success';
                } else {
                    $ret['status'] = -1;
                    $ret['message'] = 'exe sql failure';
                }
            }
        }

        Log_Util::log_info($ret, __CLASS__);

        echo json_encode($ret);
    }

    public function check_data($host_phone, $contact_list)
    {
        $message = null;
        if ($host_phone === null) {
            $message = 'host_phone不能为空';
        } else if ($contact_list != null) {
            $message = 'contacts不能为空';
            foreach ($contact_list as $contact) {
                if ($contact['phone'] === null) {
                    $message = 'contact_phone不能为空';
                    break;
                }
            }
        } else {
            $message = 'contacts不能为空';
        }
        return $message;
    }

}