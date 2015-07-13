<?php
require_once(dirname(__FILE__) . '/Card_Util.php');

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/6
 * Time: 11:44
 */
class Order_Util
{

    public static function get_borrow_state($order)
    {
        $result = Order_Util::get_borrow_state($order);
        switch ($order['state']) {
            case 1:
                $result = $result . '对方已确认借出卡';
                break;
            case 2:
                $result = $result . '对方已确认收回卡';
                break;
        }
    }

    public static function get_lend_state($order)
    {
        $result = Order_Util::get_lend_head($order);
        switch ($order['state']) {
            case 3:
                $result = $result . '对方已付款，请查收';
                break;
        }
        return $result;
    }

    public static function get_borrow_head($order)
    {
        $result = '您好，您向' . $order['lend_name'] . '借的' . $order['shop_name'] . "的";
        switch ($order['ware_type']) {
            case 0:
                $result = $result . Card_Util::get_ware_type($order['ware_type']);
            case 1:
                $result = $result . $order['discount'] . '折' . Card_Util::get_ware_type($order['ware_type']);
        }
        return $result;
    }

    public static function get_lend_head($order)
    {
        $result = '您好，您借给' . $order['borrow_name'] . '的' . $order['shop_name'] . "的";
        switch ($order['ware_type']) {
            case 0:
                $result = $result . Card_Util::get_ware_type($order['ware_type']);
            case 1:
                $result = $result . $order['discount'] . '折' . Card_Util::get_ware_type($order['ware_type']);
        }
        return $result;
    }

}