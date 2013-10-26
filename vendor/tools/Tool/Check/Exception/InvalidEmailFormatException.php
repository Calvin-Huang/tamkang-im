<?php

namespace Tool\Check\Exception;

use \Exception;

class InvalidEmailFormatException extends Exception
{
    public function __construct()
    {
        $this->code = "007";
        $this->message = "信箱格式不正確";
    }
}