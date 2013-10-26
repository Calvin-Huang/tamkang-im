<?php

namespace Tool\Sql;

use \PDO;
use \PDOException;

class SqlConnection
{
    private $lastQuery = "";
    private $lasrParam = array();
    private $config = null;
    
    public function __construct()
    {
        $this->config = require realpath(__DIR__ . "/../../../../config") . "/sqlconnection.config.php";
        // $db = new PDO('mysql:host=localhost;dbname=<SOMEDB>', '<USERNAME>', 'PASSWORD');
    }
    
    public function executeQuery($query, $param = array(), $selectOne = false)
    {
        $pdo = new PDO($this->config["dsn"], $this->config["username"], $this->config["password"]);
        $stmt = $pdo->prepare("SET NAMES UTF8");
        $stmt->execute();
        
        $response = false;
        $temp = explode(" ", $query);
        $type = $temp[0];
        
        if ($selectOne) {
            if (mb_stripos(strtoupper($query), "LIMIT 1", 0, "UTF-8") == false) {
                $query.= " LIMIT 1";
            }
        }
        
        $stmt = $pdo->prepare($query);
        
        if (count($param) > 0) {
            foreach ($param as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }   
        }
        
        $result = $stmt->execute();
        
        if ($result) {
            
            // 主要希望此method如果非正確回傳false，在使用上就確認非false為正確，也就是說錯誤確定回傳false，正確為一般資料
            switch($type) {
                case "SELECT" :
                    if ($selectOne) {
                        // 查詢只有一筆，並且找不到資料的時候會回傳false
                        $response = $stmt->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    
                    // 用全域變數儲存最後一次的查詢
                    $this->lastQuery = $query;
                    $this->lasrParam = $param;
                    break;
                case "INSERT" :
                    $response = $pdo->lastInsertId();
                    
                    $this->lastQuery = "";
                    $this->lasrParam = array();
                    break;
                default : 
                    $response = true;
                    $this->lastQuery = "";
                    $this->lasrParam = array();
            }
        } else {
            $errorInfo = $stmt->errorInfo();
            $errorCode = $stmt->errorCode();
            throw new PDOException($query, intval($errorInfo));
        }
        
        unset($pdo);
        return $response;
    }
    
    // 取得最後一次查詢的資料表內容總數量
    public function getLastQueryDataQuantity()
    {
        $pdo = new PDO($this->config["dsn"], $this->config["username"], $this->config["password"]);
        $stmt = $pdo->prepare("SET NAMES UTF8");
        $stmt->execute();
        
        $param = $this->lasrParam;
        $query = $this->lastQuery;
        $query = preg_replace("/\\sLIMIT.*/i", "", $query);
        $query = preg_replace("/\\sORDER.*/i", "", $query);
        $query = preg_replace("/SELECT\\s(.*)\\sFROM(.*)/i", "SELECT COUNT(*) AS `num` FROM\${2}", $query);
        
        $stmt = $pdo->prepare($query);
        
        if (count($param) > 0) {
            foreach ($param as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }
        }

        $result = $stmt->execute();
        
        if ($result) {
            $response = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return 0;
        }
        
        unset($pdo);
        return (int)$response["num"];
    }
}
?>