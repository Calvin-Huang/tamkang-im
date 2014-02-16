<?php

namespace Tool\Check;

use Tool\Check\Exception\InvalidEmailFormatException;
use Tool\Check\Exception\TokenNotCorrectException;
use Tool\Check\Exception\TokenEmptyException;
use Tool\Check\Exception\FieldEmptyException;
use Tool\Check\Exception\CollisionException;
use Tool\Check\Exception\EmptyAuthorizedException;
use Tool\Check\Exception\InvalidTimeFormatException;
use Tool\Check\Exception\InvalidPhoneFormatException;
use Tool\Check\Exception\InvalidBooleanValueException;
use \Memcache;
use Tool\Check\Exception\InvalidUrlFormatException;

class FieldCheck
{
    public function __construct()
    {
        
    }
    
    public function __destruct()
    {
        
    }
    
    public function checkPage($field)
    {
        if ($field == "" || !isset($field)) {
            $field = 1;
        }
        return (int)trim($field);
    }
    
    public function checkArray($field)
    {
        if (!isset($field)) {
            throw new FieldEmptyException();
        } else if (!is_array($field)) {
            return array();
        }
        
        return $field;
    }
    
    public function checkInput($field)
    {
        if ($field == "" || !isset($field)) {
            throw new FieldEmptyException();
        }
    
        $field = strip_tags($field);
        return (string)trim($field);
    }
    
    // TO-DO filter rich editor XSS
    public function checkEditor($field)
    {
        if ($field == "" || !isset($field)) {
            throw new FieldEmptyException();
        }
        
        $string = (string)trim($field);
        $string = strip_tags($string, "<h1><h2><h3><p><strong><s><em><ol><ul><li><table><thead><tbody><td><tr><a>");
        
        return (string)trim($field);
    }
    
    public function checkFile($field)
    {
        if (
            $field["name"] == "" || !isset($field["name"]) ||
            $field["tmp_name"] == "" || !isset($field["name"])
        ) {
            throw new FieldEmptyException();
        }
        
        return $field;
    }
    
    public function checkToken($token)
    {
        if (!isset($token) || $token == "") {
            throw new TokenEmptyException();
        } else if ($_SESSION["check"]->token != $token) {
    
            // Token不正確
            throw new TokenNotCorrectException();
        }
    
        unset($_SESSION["check"]);
    }
    
    public function checkCacheToken($tokenId, $token)
    {
        $cache = new Memcache();
        $cache->connect("127.0.0.1", 11211);
    
        if (!isset($token) || $token == "") {
            throw new TokenEmptyException();
        } else if ($cache->get($tokenId) != $token) {
    
            // Token不正確
            throw new TokenNotCorrectException();
        }
    
        $cache->delete($tokenId);
    }
    
    public function createToken($salt)
    {
        $token = $this->getRandomString(15) . substr(sha1($salt), 0, 5);
        
        $sessionCheck = new \stdClass();
        $sessionCheck->token = $token;
        $_SESSION["check"] = $sessionCheck;
        
        return (string)$token;
    }
    
    public function createCacheToken($salt, $time = 10)
    {
        $time = ((int)$time <= 0) ? 10 : (int)$time;
        
        $cache = new Memcache();
        $cache->connect("127.0.0.1", 11211);
    
        $token = $this->getRandomString(15) . substr(sha1($salt), 0, 5);
        $tokenId = $this->getRandomString(10);
    
        while ($cache->get($tokenId)) {
            $tokenId = $this->getRandomString(10);
        }
        
        // $memcache_obj->set('var_key', 'some really big variable', MEMCACHE_COMPRESSED, 50);
        $cache->set($tokenId, $token, false, $time);
    
        return array("token_id" => $tokenId, "token" => $token);
    }
    
    public function checkEmailFormat($email)
    {
        if (!preg_match("/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\\.([a-zA-Z]{2,4})*$/", $email)) {
            throw new InvalidEmailFormatException();
        }
        
        return (string)$email;
    }
    
    public function checkAuthorized()
    {
        if ($_SESSION["authorized"]->user == "" || !isset($_SESSION["authorized"]->id)) {
            throw new EmptyAuthorizedException();
        }
    }
    
    public function checkBoolean($boolean)
    {
        if ($boolean == "" || !isset($boolean)) {
            throw new FieldEmptyException();
        } else if ((int)$boolean > 1 || (int)$boolean < 0) {
            throw new InvalidBooleanValueException();
        }
        
        return intval($boolean);
    }
    
    public function checkTimeFormat($time)
    {
        if ($time == "" || !isset($time)) {
            throw new FieldEmptyException();
        } else if (!preg_match("/^\\d{1,2}:\\d{1,2}$/", $time)) {
            throw new InvalidTimeFormatException();
        }
        
        return (string)trim($time);
    }
    
    public function checkUrlFormat($url)
    {
        if (!isset($url) || $url == "") {
            throw new FieldEmptyException();
        } else if (!preg_match("/^(http:\/\/|https:\/\/)[\\w\\-\\_]+\\.[\\w\\-\\_]+\\..+/", $url)) {
            throw new InvalidUrlFormatException();
        }
        
        return (string)$url;
    }
    
    public function checkDateTimeFormat($dateTime)
    {
        if ($dateTime == "" || !isset($dateTime)) {
            throw new FieldEmptyException();
        } else if (!preg_match("/^\\d{4}[\\/|-]\\d{1,2}[\\/|-]\\d{1,2} \\d{1,2}:\\d{1,2}(:\\d{1,2})?$/", $dateTime)) {
            throw new InvalidTimeFormatException();
        }
    
        return (string)trim($dateTime);
    }
    
    public function checkDateFormat($date)
    {
        if ($date == "" || !isset($date)) {
            throw new FieldEmptyException();
        } else if (!preg_match("/^\\d{4}[\\/|-]\\d{1,2}[\\/|-]\\d{1,2}$/", $date)) {
            throw new InvalidTimeFormatException();
        }
    
        return (string)trim($date);
    }
    
    public function checkPhoneFormat($phone)
    {
        if ($phone == "" || !isset($phone)) {
            throw new FieldEmptyException();
        } else if (!preg_match("/^((\\(\\d{2,3}\\)\\d{7,8})|(\\d{2,3}-?\\d{7,8}))$/", $phone)) {
            throw new InvalidPhoneFormatException();
        }
        
        return (string)trim($phone);
    }
    
    public function preventRepeat($time)
    {
        $lastTime = $_SESSION["voteTime"];
        if (isset($lastTime) && (time() - $lastTime < $time)) {
    
            // 當上次投票時間距離現在時間小於time(秒)，丟出錯誤(重複發送請求時間小於設定值)
            throw new CollisionException($time);
        }
    
        // 設置&更新投票時間
        $_SESSION["voteTime"] = time();
    }
    
    private function getRandomString($length)
    {
        $default = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        $randomString = "";
    
        for ($i = 0; $i <= $length; $i++) {
            $randomNumber = rand(0, strlen($default) - 1);
            $randomString .= $default[$randomNumber];
        }
    
        return (string)$randomString;
    }
}
?>