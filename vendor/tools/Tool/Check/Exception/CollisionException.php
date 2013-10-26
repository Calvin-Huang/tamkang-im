<?php

namespace Tool\Check\Exception;

use \Exception;

class CollisionException extends Exception
{
    public function __construct($time)
    {
        $this->code = "003";
        $this->message = "重複發送請求時間小於設定值" . $time . "秒";
    }
}