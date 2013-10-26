<?php

namespace Tool\Check\Exception;

use \Exception;

class InvalidPhoneFormatException extends Exception
{
    public function __construct()
    {
        $this->message = "電話格式不正確";
        $this->code = "008";
    }
}