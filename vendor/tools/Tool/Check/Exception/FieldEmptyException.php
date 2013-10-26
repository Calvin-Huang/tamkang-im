<?php

namespace Tool\Check\Exception;

use \Exception;

class FieldEmptyException extends Exception
{
    public function __construct()
    {
        $this->code = "005";
        $this->message = "欄位為空";
    }
}