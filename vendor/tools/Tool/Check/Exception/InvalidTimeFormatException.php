<?php

namespace Tool\Check\Exception;

use \Exception;

class InvalidTimeFormatException extends Exception
{
    public function __construct()
    {
        $this->message = "時間格式不正確";
        $this->code = "009";
    }
}