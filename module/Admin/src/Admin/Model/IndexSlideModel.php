<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;
class IndexSlideModel
{
    public function __construct()
    {
    }
    
    public function addIndexSlide($image)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $image;
        
        $param[] = $this->countIndexSlide() + 1;
    
        $query = "INSERT INTO `index_slide` (`image`, `sort`) ";
        $query.= "VALUES (?, ?)";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function countIndexSlide()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT COUNT(*) AS `total` FROM `index_slide`";
        
        $result = $sqlConnection->executeQuery($query, null, true);
        
        return (int)$result["total"];
    }
    
    public function deleteIndexSlideById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        // 排序數值大於目前要刪除的順序減一
        $param[] = $this->getIndexSlideSortById($id);
        $query = "UPDATE `index_slide` SET `sort` = `sort` - 1 ";
        $query.= "WHERE `sort` > ?";
        
        $sqlConnection->executeQuery($query, $param);
        
        $param = array();
        $param[] = $id;
        $query = "DELETE FROM `index_slide` WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function getIndexSlideById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `id`, `image` FROM `index_slide`";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function getIndexSlideSortById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query.= "SELECT `sort` FROM `index_slide` ";
        $query.= "WHERE `id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return (int)$result["sort"];
    }
    
    public function getSlideImageById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `image` FROM `index_slide` ";
        $query.= "WHERE `id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["image"];
    }
    
    public function listIndexSlide()
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT `id`, `sort`, `image` FROM `index_slide` ";
        $query.= "ORDER BY `sort`";
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setIndexSlideById($id, $image = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $condition = array();
        
        $query = "UPDATE `index_slide` SET ";
        
        if (isset($image) && $image != "") {
            $param[] = $image;
            $condition[] = "`image` = ?";
        }

        $query.= join(", ", $condition) . " ";
        
        $param[] = $id;
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setIndexSlideSort($sort)
    {
        $sqlConnection = new SqlConnection();
        $query = "UPDATE `index_slide` SET `sort` = ? WHERE `id` = ?";
        
        foreach ($sort as $i => $id) {
            $param = array();
            
            $param[] = $i + 1;
            $param[] = $id;
            $sqlConnection->executeQuery($query, $param);
        }
    }
}