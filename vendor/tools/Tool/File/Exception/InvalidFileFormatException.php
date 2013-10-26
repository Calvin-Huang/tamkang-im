<?php

namespace Tool\File\Exception;

use \Exception;

class InvalidFileFormatException extends Exception
{
    public function __construct($message = "上傳檔案非合法格式")
    {
        $this->message = $message;
    }
}