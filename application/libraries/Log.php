<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/12
 * Time: 12:13
 */
class Log
{
    //
    private static $instance = null;
    //
    private static $handle = null;
    //
    private $log_switch = null;
    //
    private $log_file_path;
    //
    private $log_max_len;
    //
    private $log_file_pre;

    private function __construct()
    {
        $this->log_file_path = 0;
        $this->log_switch = 0;
        $this->log_max_len = 0;
    }

    public static function get_instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function log($type, $desc, $time)
    {
        if ($this->log_switch) {
            if (self::$handle == null) {
                $filename = $this->log_file_pre . $this->get_max_log_file_suf();
                self::$handle = ($this->log_file_path . $filename);
            }

            switch ($type) {
                case 0:
                    fwrite(self::$handle, 'THING_LOG' . ' ' . $desc . ' ' . $time, 13);
                    break;
                case 1:
                    fwrite(self::$handle, 'ERROR_LOG' . ' ' . $desc . ' ' . $time, 13);
                    break;
                default:
                    fwrite(self::$handle, 'THING_LOG' . ' ' . $desc . ' ' . $time, 13);
                    break;
            }
        }
    }

    public function get_max_log_file_suf()
    {
        $log_file_suf = null;
        if ($this->log_file_path) {
            if ($dh = opendir($this->log_file_path)) {
                while (($file = readdir($dh) != FALSE)) {
                    if ($file != '.' && $file != '..') {
                        if ($this->log_file_path . $file == 'file') {
                            $rs = split('_', $file);
                            if ($log_file_suf < $rs[1]) {
                                $log_file_suf = $rs[1];
                            }
                        }
                    }
                }

                if ($log_file_suf == null) {
                    $log_file_suf = 0;
                }

                if (($this->log_file_path . $this->log_file_pre . $log_file_suf)
                    && ($this->log_file_path . $this->log_file_pre . $log_file_suf) > $this->log_max_len
                ) {
                    $log_file_suf = $log_file_suf + 1;
                }

                return $log_file_suf;
            }
        }
        return 0;
    }

    public function close()
    {
        fclose(self::$handle);
    }
}