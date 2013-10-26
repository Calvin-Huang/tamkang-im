<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class AdmissionModel
{
    public function __construct()
    {
        
    }
    
    public function addIntroduce($typeId, $content)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $typeId;
        $param[] = $content;
        $query = "INSERT INTO `admission` (`type_id`, `content`) VALUES (?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteIntroduceById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `admission` WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        $query = "DELETE FROM `admission_type`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function getIntroduceById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `content`, `type_id` FROM `admission` ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function getIntroduceByTypeId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $param[] = $id;
        $query = "SELECT C.`content` FROM `admission` AS C ";
        $query.= "JOIN `admission_type` AS CT ON C.`type_id` = CT.`id` AND CT.`id` = ? ";
        $query.= "WHERE C.`type_id` = ? ";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function listIntroduce()
    {
        $sqlConnection = new SqlConnection();
        
        $query = "SELECT A.`id`, A.`type_id`, AT.`type_name`, A.`content` FROM `admission` AS A ";
        $query.= "JOIN `admission_type` AS AT ON A.`type_id` = AT.`id`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        
        $query = "SELECT `id`, `type_name` FROM `admission_type`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listNotExistsIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT CT.`id`, CT.`type_name` FROM `admission_type` CT ";
        $query.= "LEFT JOIN `admission` C ON C.`type_id` = CT.`id` ";
        $query.= "WHERE C.`type_id` IS NULL";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function setIntroduceById($id, $typeId, $content)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $typeId;
        $param[] = $content;
        $param[] = $id;
        $query = "UPDATE `admission` SET `type_id` = ?, `content` = ? ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setIntroduceType($types)
    {
        // 把所有的種類清除，重新設定自動增號之後insert
        $this->deleteIntroduceType();
        
        $sqlConnection = new SqlConnection();
        $query = "ALTER TABLE `admission_type` AUTO_INCREMENT = 1";
        $sqlConnection->executeQuery($query);
        
        $query = "INSERT INTO `admission_type` (`type_name`) VALUES (?)";
        
        // 紀錄種類
        foreach ($types as $i => $type) {
            if (isset($type) && $type != "") {
                $param = array();
                
                $param[] = $type;
                $sqlConnection->executeQuery($query, $param);
            }
        }
        
        return true;
    }
}