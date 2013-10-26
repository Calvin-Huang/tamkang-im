<?php

namespace Tool\Encrypt;

class Encrypt
{
    private $key = "";
    
    public function __construct()
    {
        $config = null;
        
        // 如果存在設定檔的話使用這定檔裡的值key
        $configFile = realpath(__DIR__ . "/../../../../config") . "/encrypt.config.php";
        if (file_exists($configFile)) {
            $config = require_once $configFile;
            $this->key = $config["key"];
        } else {
            $this->key = "this is default key";
        }
    }
    
    public function twofishEncrypt($data)
    {
        $td = mcrypt_module_open("twofish", "", "ecb", "");
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->key, $iv);
        
        $encryptedData = mcrypt_generic($td, $data);
        
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        
        return bin2hex($encryptedData);
    }
    
    public function twofishDecrypt($encryptedData)
    {
        
        
        $td = mcrypt_module_open("twofish", "", "ecb", "");
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->key, $iv);
        
        $encryptedData = pack("H*", $encryptedData);
        $decryptedData = mdecrypt_generic($td, $encryptedData);
        
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        
        return $decryptedData;
    }
}