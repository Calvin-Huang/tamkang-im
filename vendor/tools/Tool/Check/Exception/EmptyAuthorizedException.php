<?php

namespace Tool\Check\Exception;

use \Exception;

class EmptyAuthorizedException extends Exception
{
    public function __construct()
    {
        $this->code = "004";
        $this->message = "尚未登入";
    }
}
?>