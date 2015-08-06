<?php
require_once(dirname(__FILE__) . '/Ret.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/8/4
 * Time: 16:50
 */
class Ret_Factory
{

    public static function create_ret($status, $message = null, $data = null)
    {
        $ret = null;
        switch ($status) {
            case -3:
                $ret = new Ret($status, $message, $data);
                break;
            case -2:
                $ret = new Ret($status, 'failure');
                break;
            case -1:
                $ret = new Ret($status, $message);
                break;
            case 0:
                $ret = new Ret($status, 'success', $data);
                break;
            case 2:
                $ret = new Ret($status, 'not login');
                break;
        }
        return $ret;
    }
}