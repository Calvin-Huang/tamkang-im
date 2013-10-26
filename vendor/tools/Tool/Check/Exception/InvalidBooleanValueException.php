<?php

namespace Tool\Check\Exception;

use \Exception;

class InvalidBooleanValueException extends Exception
{
    public function __construct()
    {
        $this->code = "006";
        $this->message = "非法布林值";
    }
}