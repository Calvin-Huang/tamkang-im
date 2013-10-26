<?php

namespace Tool\Sql;

use \PDO;
use \PDOException;

class SqliteConnection
{
    private $config = null;
    public function __construct()
    {
        $this->config = require_once realpath(__DIR__ . "/../../../../config") . "/sqliteconnection.config.php";
    }
    
    public function __destruct()
    {
        // 更動log檔版本+1
        $logFile = realpath(__DIR__ . "/../../../..") . "/log/sqlite.log";
        $logVersion = file_get_contents($logFile);
        file_put_contents($logFile, intval($logVersion) + 1);
    }
    
    public function executeQuery($query, $param = array(), $selectOne = false)
    {
        $pdo = new PDO($this->config["location"]);
        
        $response = false;
        $temp = explode(" ", $query);
        $type = $temp[0];
        
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
                    break;
                case "INSERT" :
                    $response = $pdo->lastInsertId();
                    break;
                default : 
                    $response = true;
            }
            
        } else {
            $errorInfo = $stmt->errorInfo();
            $errorCode = $stmt->errorCode();
            throw new PDOException($query, intval($errorInfo));
        }
        
        unset($pdo);
        return $response;
    }
}
?>