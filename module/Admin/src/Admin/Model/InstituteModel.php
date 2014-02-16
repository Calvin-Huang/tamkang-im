<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class InstituteModel
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
        $query = "INSERT INTO `institute` (`type_id`, `content`) VALUES (?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteIntroduceById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `institute` WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        $query = "DELETE FROM `institute_type`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function getIntroduceById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `content`, `type_id` FROM `institute` ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function getIntroduceByTypeId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $param[] = $id;
        $query = "SELECT C.`content` FROM `institute` AS C ";
        $query.= "JOIN `institute_type` AS CT ON C.`type_id` = CT.`id` AND CT.`id` = ? ";
        $query.= "WHERE C.`type_id` = ? ";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function listIntroduce()
    {
        $sqlConnection = new SqlConnection();
        
        $query = "SELECT C.`id`, C.`type_id`, CT.`type_name`, C.`content` FROM `institute` AS C ";
        $query.= "JOIN `institute_type` AS CT ON C.`type_id` = CT.`id`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listIntroduceType($languageId = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT `id`, `type_name`, `language_id` FROM `institute_type` ";
        
        if ($languageId != null) {
            $param[] = $languageId;
            $query.= "WHERE `language_id` = ?";
        }
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listNotExistsIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT IT.`id`, IT.`type_name` FROM `institute_type` IT ";
        $query.= "LEFT JOIN `institute` I ON I.`type_id` = IT.`id` ";
        $query.= "WHERE I.`type_id` IS NULL";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function setIntroduceById($id, $typeId, $content)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $typeId;
        $param[] = $content;
        $param[] = $id;
        $query = "UPDATE `institute` SET `type_id` = ?, `content` = ? ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setIntroduceType($types, $languages)
    {
        // 把所有的種類清除，重新設定自動增號之後insert
        $this->deleteIntroduceType();
        
        $sqlConnection = new SqlConnection();
        $query = "ALTER TABLE `institute_type` AUTO_INCREMENT = 1";
        $sqlConnection->executeQuery($query);
        
        $query = "INSERT INTO `institute_type` (`type_name`, `language_id`) VALUES (?, ?)";
        
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