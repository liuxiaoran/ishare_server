<?php
date_default_timezone_set("PRC");

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/14
 * Time: 10:30
 */
class Log_Util
{

    public static function log_param($data, $label)
    {
        $message = date("[Y-m-d H:i:s]") . $label . ': ';
        $message = $message . self::get_data_str($data) . "\n";
        error_log($message, 3, self::get_log_des(0));
    }

    public static function get_data_str($data)
    {
        $result = '';
        foreach ($data as $key => $value) {
            $result = $result . $key . '=' . json_encode($value) . '&';
        }
        return $result;
    }

    public static function log_info($data, $label)
    {
        $message = date("[Y-m-d H:i:s]") . $label . ': ';
        $message = $message . self::get_data_str($data) . "\n";
        error_log($message, 3, self::get_log_des(1));
    }

    public static function log_sql($sql, $label)
    {
        $message = date("[Y-m-d H:i:s]") . $label . ': ';
        $message = $message . $sql . "\n";
        error_log($message, 3, self::get_log_des(2));
    }

    public static function log_sql_exc($sql, $label)
    {
        $message = date("[Y-m-d H:i:s]") . $label . ': ';
        $message = $message . $sql . "\n";
        error_log($message, 3, self::get_log_des(3));
    }

    public static function get_log_des($type, $base_path = '/data/wwwroot/log')
    {
        $path = $base_path . self::get_mid_path($type);
        return self::get_file_name($path);
    }

    public static function get_file_name($path, $suffix = '.log', $size = 5120)
    {
        $file_name = null;
        for ($i = 0; ; $i++) {
            $file_name = $path . "/" . self::get_date() . "_" . $i . $suffix;
            if (file_exists($file_name)) {
                if (abs(filesize($file_name)) < $size) {
                    break;
                }
            } else {
                $file = fopen($file_name, "w");
                fclose($file);
                break;
            }
        }

        return $file_name;
    }

    public static function get_mid_path($type)
    {
        $file_name = null;
        switch ($type) {
            case 0:
                $file_name = '/param';
                break;
            case 1:
                $file_name = '/info';
                break;
            case 2:
                $file_name = '/sql';
                break;
            case 3:
                $file_name = '/sql_exc';
                break;
            default:
                $file_name = '/info';
                break;
        }

        return $file_name;
    }

    public static function get_date()
    {
        return date('Y-m-d');
    }
}