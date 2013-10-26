<?php

namespace Tool\Check\Exception;

use \Exception;

class InvalidUrlFormatException extends Exception
{
    public function __construct()
    {
        $this->code = "010";
        $this->message = "Url格式錯誤";
    }
}