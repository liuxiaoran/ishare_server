<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/6/6
 * Time: 12:28
 */
class Card_Util
{

    public static function get_ware_type($ware_type)
    {
        $result = null;
        switch ($ware_type) {
            case 0:
                $result = '充值卡';
                break;
            case 1:
                $result = '会员卡';
                break;
        }
        return $result;
    }
}