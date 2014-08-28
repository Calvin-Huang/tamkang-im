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
    
    public function addType($params)
    {
        if (isset($params["name"]) && $params["name"] != "") {
            $sqlConnection = new SqlConnection();
            $query = "INSERT INTO `admission_type`(`type_name`, `language_id`, `sort`) VALUES (?, ?, ?)";
            $conditions[] = $params["name"];
            $conditions[] = $params["language"];
            $conditions[] = $this->getLastSortFromType() + 1;

            return $sqlConnection->executeQuery($query, $conditions);
        } else {
            return false;
        }
    }

    public function deleteTypeById($id)
    {
        $sqlConnection = new SqlConnection();

        $query = "UPDATE `admission_type` SET `sort` = `sort` - 1 WHERE `sort` > ?";
        $conditions[] = $this->getSortFromTypeById($id);
        $sqlConnection->executeQuery($query, $conditions);

        $conditions = array();

        $query = "DELETE FROM `admission_type` WHERE `id` = ?";
        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions);
    }

    public function deleteIntroduceById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `admission` WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public  function exists($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `id` FROM `admission_type` WHERE `id` = ?";
        $param[] = $id;

        return $sqlConnection->executeQuery($query, $param, true) != null;
    }

    public function getSortFromTypeById($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `sort` FROM `admission_type` WHERE `id` = ?";
        $conditions[] = $id;

        $result = $sqlConnection->executeQuery($query, $conditions, true);

        return $result["sort"];
    }

    public function getLastSortFromType()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `sort` FROM `admission_type` ORDER BY `sort` DESC LIMIT 1";
        $result = $sqlConnection->executeQuery($query, null, true);

        return (int)$result["sort"];
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
        
        $query = "SELECT C.`id`, C.`type_id`, CT.`type_name`, CT.`sort`, C.`content` FROM `admission` AS C ";
        $query.= "JOIN `admission_type` AS CT ON C.`type_id` = CT.`id` ORDER BY CT.`sort` ";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listIntroduceType($languageId = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT `id`, `type_name`, `language_id`, `sort` FROM `admission_type` ";
        if ($languageId != null) {
            $param[] = $languageId;            
            $query.= "WHERE `language_id` = ?";
        }
        $query .= " ORDER BY `sort`";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listNotExistsIntroduceType()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT CT.`id`, CT.`type_name` FROM `admission_type` CT ";
        $query.= "LEFT JOIN `admission` C ON C.`type_id` = CT.`id` ";
        $query.= "WHERE C.`type_id` IS NULL";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function updateTypeAll($params)
    {
        $sqlConnection = new SqlConnection();
        foreach ($params["type"] as $k => $v) {
            if ($this->exists($v["id"])) {
                if ($v["_delete"]) {
                    $this->deleteTypeById($v["id"]);
                } else {
                    $this->updateType($v);
                }
            } else {
                $this->addType($v);
            }
        }

        return true;
    }

    public function updateType($params)
    {
        $sqlConnection = new SqlConnection();
        $query = "UPDATE `admission_type` SET `type_name` = ?, `language_id` = ?, `sort` = ?  WHERE `id` = ?";

        $conditions[] = $params["name"];
        $conditions[] = $params["language"];
        $conditions[] = $params["sort"];
        $conditions[] = $params["id"];

        return $sqlConnection->executeQuery($query, $conditions, true);
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
}