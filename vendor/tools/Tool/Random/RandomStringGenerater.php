<?php

namespace Tool\Random;

class RandomStringGenerater
{
    public function getSimpleString($length)
    {
        $default = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        $randomString = "";
    
        for ($i = 0; $i < $length; $i++) {
            $randomNumber = rand(0, strlen($default) - 1);
            $randomString .= $default[$randomNumber];
        }
    
        return $randomString;
    }
}