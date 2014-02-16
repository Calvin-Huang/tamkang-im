<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class RandomStringLog
{
    public function __construct(){
        
    }
    
    public function addRadomStringLog($string)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $string;
        $query = "INSERT INTO `random_string_log`(`string`) VALUES (?)";
        $result = $sqlConnection->executeQuery($query, $param);
    
        return $result;
    }
    
    public function deleteRandomStringLog($string)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $string;
        $query = "DELETE FROM `random_string_log` WHERE `string` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function existsStringLog($string)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $string;
        $query = "SELECT `id` FROM `random_string_log` WHERE `string` = ?";
        $result = $sqlConnection->executeQuery($query, $param, true);
        if ($result != false) {
            return true;
        }
        
        return false;
    }
}