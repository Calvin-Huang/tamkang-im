<?php

namespace Tool\Mail\Exception;

use \Exception;

class EmptyReceiverException extends Exception
{
    public function __construct()
    {
        $this->message = "未指定收件者";
    }
}