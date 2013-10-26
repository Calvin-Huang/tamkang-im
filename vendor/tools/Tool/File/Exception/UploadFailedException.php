<?php

namespace Tool\File\Exception;

use \Exception;

class UploadFailedException extends Exception
{
    public function __construct($message = "上傳失敗")
    {
        $this->message = $message;
    }
}