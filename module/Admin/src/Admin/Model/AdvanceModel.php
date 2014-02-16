<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class AdvanceModel
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
        $query = "INSERT INTO `advance` (`type_id`, `content`) VALUES (?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteIntroduceById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `advance` WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        $query = "DELETE FROM `advance_type`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function getIntroduceById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `content`, `type_id` FROM `advance` ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function getIntroduceByTypeId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $param[] = $id;
        $query = "SELECT C.`content` FROM `advance` AS C ";
        $query.= "JOIN `advance_type` AS CT ON C.`type_id` = CT.`id` AND CT.`id` = ? ";
        $query.= "WHERE C.`type_id` = ? ";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function listIntroduce()
    {
        $sqlConnection = new SqlConnection();
        
        $query = "SELECT A.`id`, A.`type_id`, AT.`type_name`, A.`content` FROM `advance` AS A ";
        $query.= "JOIN `advance_type` AS AT ON A.`type_id` = AT.`id`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listIntroduceType($languageId = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT `id`, `type_name`, `language_id` FROM `advance_type` ";
        
        if ($languageId != null) {
            $param[] = $languageId;
            $query.= "WHERE `language_id` = ?";
        }
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listNotExistsIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT CT.`id`, CT.`type_name` FROM `advance_type` CT ";
        $query.= "LEFT JOIN `advance` C ON C.`type_id` = CT.`id` ";
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
        $query = "UPDATE `advance` SET `type_id` = ?, `content` = ? ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setIntroduceType($types, $languages)
    {
        // 把所有的種類清除，重新設定自動增號之後insert
        $this->deleteIntroduceType();
        
        $sqlConnection = new SqlConnection();
        $query = "ALTER TABLE `advance_type` AUTO_INCREMENT = 1";
        $sqlConnection->executeQuery($query);
        
        $query = "INSERT INTO `advance_type` (`type_name`, `language_id`) VALUES (?, ?)";
        
        // 紀錄種類
        foreach ($types as $i => $type) {
            if (isset($type) && $type != "") {
                $param = array();
                
                $param[] = $type;
                $param[] = $languages[$i];
                $sqlConnection->executeQuery($query, $param);
            }
        }
        
        return true;
    }
}