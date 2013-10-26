<?php

namespace Tool\Security;

class AccessSecurity
{
    private $keyHex = "20C86125F86DB932D0139D32D9208CEF52BAEC98F1E9BA2A"; // 金鑰, 48字元全大寫, 16進位
    private $ivHex = "0102030405060AAA"; // 初始向量, 16 字元全大寫, 16進位
    private $key = "";
    private $iv = "";
    private $secret = "tamkang-im";
    private $leftPaddingLength = 4;
    private $rightPaddingLength = 5;
    
    // access token有效時間
    private $expireDate = "1";
    
    public function __construct()
    {
        $this->key = pack("H48", $this->keyHex);
        $this->iv = pack("H16", $this->ivHex);
    }
    
    public function encrypt($value)
    {
        // 加密模式为ECB
        $td = mcrypt_module_open(MCRYPT_3DES, "", MCRYPT_MODE_ECB, "");
        $value = $this->PaddingPKCS7($value);
    
        // 填充
        mcrypt_generic_init($td, $this->key, $this->iv);
        $mg = mcrypt_generic($td, $value);
        $ret = $this->PaddingBase64($mg);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $ret;
    }
    
    public function decrypt($value)
    {
        $td = mcrypt_module_open(MCRYPT_3DES, "", MCRYPT_MODE_ECB, "");
        mcrypt_generic_init($td, $this->key, $this->iv);
        $ret = trim(mdecrypt_generic($td, trim(base64_decode($value))));
        $ret = $this->UnPaddingPKCS7($ret);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $ret;
    }
    
    public function createAccessToken($memberId, $time)
    {
        $cipher = "";
        do {
            $rand1 = $this->randomStr(rand(2, 15));
            $rand2 = $this->randomStr(rand(2, 15));
            $rand3 = $this->randomStr(rand(2, 15));
            $rand4 = $this->randomStr(rand(2, 15));
            $rand5 = $this->randomStr(rand(2, 15));
            $plain = $rand1 . "|" . $memberId . "|" . $rand2 . "|" . $rand3 . "|" . $time . "|" . $rand4 . "|" . $this->secret . "|" . $rand5;
            $cipher = $this->encrypt($plain);
            $cipher = $this->paddingSalt($cipher);
            $cipher = str_replace("+", "-", $cipher);
        } while (!$this->checkSecret($cipher));
        return $cipher;
    }
    
    public function checkAccessToken($token)
    {
        $base64token = $this->removeSalt($token);
        $cipher = str_replace("-", "+", $base64token);
    
        $plain = $this->decrypt($cipher);
        try {
            $strAry = explode("|", $plain);
    
            if (count($strAry) != 8) {
                
                // Token Secret 驗證不合法(字段不合法)
                throw new \Exception("Token Secret 驗證不合法(字段不合法)");
            } else {
                list($rand1, $memberId, $rand2, $rand3, $time, $rand4, $secret, $rand5) = $strAry;
            }
        } catch (\Exception $ex) {
            
            // Token Secret 驗證不合法(格式有誤)
            throw new \Exception("Token Secret 驗證不合法(格式有誤)");
        }
    
        if ($secret != $this->secret) {
            
            // Token Secret 驗證不合法
            throw new \Exception("Token Secret 驗證不合法");
        }
    
        if ($this->dateDiff($time) > $this->expireDate) {
            
            // Token過期
            throw new \Exception("Token過期");
        }
    
        if (!is_numeric($memberId)) {
            
            // Token (member_id)驗證不合法
            throw new \Exception("Token (member_id)驗證不合法");
        }
        return $memberId;
    }
    
    private function PaddingPKCS7($data)
    {
        $padlen = 8 - strlen($data) % 8;
        for ($i = 0; $i < $padlen; $i++) {
            $data .= chr($padlen);
        }
        return $data;
    }
    
    private function UnPaddingPKCS7($data)
    {
        $padlen = ord(substr($data, (strlen($data) - 1), 1));
        if ($padlen > 8) {
            return $data;
        }
        for ($i = -1 * ($padlen - strlen($data)); $i < strlen($data); $i++) {
            if (ord(substr($data, $i, 1)) != $padlen) {
                return false;
            }
        }
        return substr($data, 0, -1 * ($padlen - strlen($data)));
    }
    
    private function PaddingBase64($str)
    {
        $base64_str = base64_encode($str);
        while ($this->endsWith($base64_str, "=")) {
            $str .= " ";
            $base64_str = base64_encode($str);
        }
        return $base64_str;
    }
    
    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        $start = $length * -1;
    
        // negative
        return (substr($haystack, $start) === $needle);
    }
    
    private function randomStr($length)
    {
        $random = "";
        srand((double)microtime() * 1000000);
        $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char_list .= "abcdefghijklmnopqrstuvwxyz";
        $char_list .= "1234567890";
    
        for ($i = 0; $i < $length; $i++) {
            $random .= substr($char_list, (rand() % (strlen($char_list))), 1);
        }
        return $random;
    }
    
    private function dateDiff($strDate1)
    {
        $strDate2 = date("Y-m-d H:i:s");
    
        // 现在时间
        return (strtotime($strDate2) - strtotime($strDate1)) / (60 * 60 * 24);
    }
    
    private function checkSecret($token)
    {
        $base64token = $this->removeSalt($token);
        $cipher = str_replace("-", "+", $base64token);
        $plain = $this->decrypt($cipher);
    
        try {
            $strAry = explode("|", $plain);
            if (count($strAry) != 8) {
                return false;
            } else {
                list($rand1, $memberId, $rand2, $rand3, $time, $rand4, $secret, $rand5) = $strAry;
                if ($secret === $this->secret) {
                    return true;
                }
            }
        } catch (\Exception $ex) {
            return false;
        }
        return false;
    }
    
    private function paddingSalt(&$cipher)
    {
        return $this->randomStr($this->leftPaddingLength) . $cipher . $this->randomStr($this->rightPaddingLength);
    }
    
    private function removeSalt(&$token)
    {
        return substr(substr($token, $this->leftPaddingLength), 0, (-1 * $this->rightPaddingLength));
    }
}