<?php

namespace Tool\Check\Exception;

use \Exception;

class TokenNotCorrectException extends Exception
{
    public function __construct()
    {
        $this->code = "012";
        $this->message = "驗證碼錯誤";
    }
}