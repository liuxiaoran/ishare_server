<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/8/3
 * Time: 16:56
 */
class Param_Util
{

    private $PARAM_UNKNOWN_ERROR = 'param is error';
    private $PARAM_ERROR = ' is error';
    private $PARAM_JSON_ERROR = ' is not json';
    private $PARAM_IS_NULL = ' is null';

    public function get_param($param_names, $params)
    {
        $result = array();
        foreach ($param_names as $para_name) {
            if (isset($params[$para_name])) {
                $result[$para_name] = $params[$para_name];
            }
        }
        return $result;
    }

    public function check_param($all_params, $params, $param_need_names)
    {
        $message = $this->check_param_null($all_params, $param_need_names);
        if ($message != null) {
            $message = $this->check_param_type($params);
        }
        return $message;
    }

    public function check_param_type($params)
    {
        $message = null;
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'open_id':
                case 'key':
                    if (!is_string($value)) {
                        $message = $this->$PARAM_UNKNOWN_ERROR;
                    }
                    break;
                case 'shop_name':
                case 'shop_location':
                case 'description':
                case 'from_user':
                case 'to_user':
                case 'borrow_id':
                case 'lend_id':
                case 'to_id':
                case 'user':
                case 'user_openid':
                case 'server_openid':
                case 'real_name':
                case 'work_unit':
                case 'nickname':
                    if (!is_string($value)) {
                        $message = $key . $this->PARAM_ERROR;
                    }
                    break;
                case 'location_id':
                case 'card_id':
                case 'page_num':
                case 'page_size':
                case 'order_id':
                case 'size':
                case 'collection_id':
                case 'id':
                case 'item_id':
                case 'request_id':
                    if (!is_numeric($value)) {
                        $message = $key . $this->PARAM_ERROR;
                    }
                    break;
                case 'longitude':
                case 'user_longitude':
                case 'shop_longitude':
                    if (!$this->check_longitude($value)) {
                        $message = $key . ' must be float and (>= -180 and <= 180)';
                    }
                    break;
                case 'latitude':
                case 'user_latitude':
                case 'shop_latitude':
                    if (!$this->check_latitude($value)) {
                        $message = $key . ' must be float and (>= -90 and <= 90)';
                    }
                    break;
                case 'per_photo':
                case 'id_facade':
                case 'id_back':
                case 'work_card':
                case 'avatar':
                    if (!$this->check_url($value)) {
                        $message = $key . $this->PARAM_ERROR;
                    }
                    break;
                case 'ware_type':
                    if (!$this->check_ware_type($value)) {
                        $message = $key . ' must be 0 or 1';
                    }
                    break;
                case 'discount':
                    if (!$this->check_discount($value)) {
                        $message = $key . ' must be >= 0 and <= 10';
                    }
                    break;
                case 'service_charge':
                    if (!$this->check_service_charge($value)) {
                        $message = $key . ' must be >= 0 and <= 10';
                    }
                    break;
                case 'trade_type':
                    if (!$this->check_trade_type($value)) {
                        $message = $key . ' must be int and (>= 0 and <= 4)';
                    }
                    break;
                case 'img':
                    if (!$this->is_json($value)) {
                        $message = $key . $this->$PARAM_JSON_ERROR;
                    }
                    break;
                case 'type':
                    if (!$this->check_type($value)) {
                        $message = $key . 'must be int and (>= 0 and <= 4)';
                    }
                    break;
                case 'card_type':
                    if (!$this->check_card_type($value)) {
                        $message = $key . 'must be int and (== 1 or == 2)';
                    }
                    break;
                case 'time':
                    if (!$this->is_date($value)) {
                        $message = $key . $this->PARAM_ERROR;
                    }
                    break;
                case 'rating':
                    if (!$this->check_rating($value)) {
                        $message = $key . ' must be int and (.= 0 and <= 10)';
                    }
                    break;
                case 'status':
                    if (!$this->check_status($value)) {
                        $message = $key . 'must be int and (>= 0 and <= 4)';
                    }
                    break;
                case 'kilometer':
                    if (!$this->check_kilometer($value)) {
                        $message = $key . $this->PARAM_ERROR;
                    }
                    break;
                case 'phone':
                    if (!$this->check_phone($value)) {
                        $message = $key . $this->PARAM_ERROR;
                    }
                    break;
                case 'gender':
                    if (!$this->check_gender($value)) {
                        $message = $key . $this->PARAM_ERROR;
                    }
                    break;
            }
            if (!is_null($message)) {
                break;
            }
        }


        return $message;
    }

    public function check_param_null($params, $param_names)
    {
        foreach ($param_names as $param_name) {
            if (isset($params[$param_name])) {
                return $param_name . $this->PARAM_IS_NULL;
            }
        }
        return null;
    }

    public function check_ware_type($param)
    {
        return is_numeric($param) && ($param == 0 || $param == 1) ? true : false;
    }

    public function check_discount($param)
    {
        return $this->check_float($param) && ($param >= 0 && $param <= 10);
    }

    public function check_service_charge($param)
    {
        return $this->check_float($param) && ($param >= 0 && $param <= 10);
    }

    public function check_trade_type($param)
    {
        return is_numeric($param) && ((int)$param >= 0 && (int)$param <= 4);
    }

    public function check_longitude($param)
    {

        return $this->check_float($param) && ((float)$param >= -180 && (float)$param <= 180);
    }

    public function check_latitude($param)
    {
        return $this->check_float($param) && ((float)$param >= -90 && (float)$param <= 90);
    }

    public function check_type($param)
    {
        return is_numeric($param) && ($param >= 0 && $param <= 4);
    }

    public function check_card_type($param)
    {
        return is_numeric($param) && ($param == 1 && $param == 2);
    }

    public function check_rating($param)
    {
        return is_numeric($param) && ((int)$param >= 0 && (int)$param <= 10);
    }

    public function check_status($param)
    {
        return is_numeric($param) && ($param >= 0 && $param <= 4);
    }

    public function is_json($param)
    {
        json_decode($param);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function check_kilometer($param)
    {
        return is_numeric($param) && ($param == 3 || $param == 5);
    }

    function is_date($param)
    {
        return strtotime($param) !== false;
    }

    function check_url($url)
    {
        return preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $url);
    }

    public function check_phone($param)
    {
        return preg_match("/1[3458]{1}\d{9}$/", $param);
    }

    public function check_gender($param)
    {
        return $param == '男' || $param == '女';
    }

    public function check_float($param)
    {
        return true;
//        return preg_match("/^\d*[.]\d*$/",$param) == 1;
    }

}