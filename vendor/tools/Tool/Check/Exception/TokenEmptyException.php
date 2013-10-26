<?php

namespace Tool\Check\Exception;

use \Exception;

class TokenEmptyException extends Exception
{
    public function __construct()
    {
        $this->code = "011";
        $this->message = "驗證碼不存在";
    }
}