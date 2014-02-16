<?php

namespace Tool\Curl;

class CurlTool
{
    private $applicationSettingAry = null;
    
    public function __construct()
    {
    }
    
    //返回網頁字串
    public static function getCurl($urlStr, $header = array(
                "Accept : text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Connection : keep-alive",
                "Cache-Control : max-age=0"
        ), $useProxyBoo = false)
    {
        $curlObj = curl_init();
    
        curl_setopt($curlObj, CURLOPT_URL, $urlStr);
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 30);
        
        $cookieFileStr = dirname(__FILE__) . "/cookie/" . preg_replace("/http[s]?:\\/\\/.*\\.(\\w*)\\..*\\/.*/", "\${1}", $urlStr) . ".txt";
        
        //存在cookie就使用cookie
        if (file_exists($cookieFileStr)) {
            curl_setopt($curlObj, CURLOPT_COOKIEFILE, $cookieFileStr);
        }
        
        //預設不使用proxy
        if ($useProxyBoo) {
            curl_setopt($curlObj, CURLOPT_PROXY, "proxy.hinet.net:80");
        }

        //Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0
        curl_setopt($curlObj, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0");
    
        //Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17
        //curl_setopt($curlObj, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17");
    
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlObj, CURLOPT_FOLLOWLOCATION, true);
    
        curl_setopt($curlObj, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $header);
    
        //簡易建立https連線的作法
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
    
        $pageObj = curl_exec($curlObj);
    
        curl_close($curlObj);
        unset($curlObj, $cookieFileStr);
    
        return $pageObj;
    }
    
    //儲存cookie以免被ban
    public function getCookie($urlStr)
    {
        $curlObj = curl_init();
    
        curl_setopt($curlObj, CURLOPT_URL, $urlStr);
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 30);
    
        //Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0
        curl_setopt($curlObj, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0");
    
        //Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17
        //curl_setopt($curlObj, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17");
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlObj, CURLOPT_FOLLOWLOCATION, true);
    
        if (!is_dir(dirname(__FILE__) . "/cookie/")) {
            mkdir(dirname(__FILE__) . "/cookie/", 0775, true);
        }

        //設定存放cookie的地方
        curl_setopt($curlObj, CURLOPT_COOKIEJAR, dirname(__FILE__) . "/cookie/" . preg_replace("/http[s]?:\/\/.*\.(\w*)\..*\/.*/", "\${1}", $urlStr) . ".txt");
    
    
        curl_setopt($curlObj, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($curlObj, CURLOPT_HEADER, array(
                "Accept : text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Connection : keep-alive",
                "Cache-Control : max-age=0"
        ));
    
        //簡易建立https連線的作法
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
    
        $pageObj = curl_exec($curlObj);
    
        curl_close($curlObj);
        
        unset($curlObj, $pageObj);
    }
}

?>
