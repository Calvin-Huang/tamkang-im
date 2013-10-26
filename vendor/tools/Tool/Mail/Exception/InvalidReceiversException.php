<?php

namespace Tool\Mail\Exception;

use \Exception;

class InvalidReceiversException extends Exception
{
    public function __construct()
    {
        $this->message = "收信者格式錯誤";
    }
}