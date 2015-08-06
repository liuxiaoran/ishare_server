<?php

/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/8/4
 * Time: 16:51
 */
class Ret
{
    public $status;
    public $message;
    public $data;

    public function __construct($status, $message, $data = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
}