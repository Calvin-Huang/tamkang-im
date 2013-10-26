<?php

namespace Application\Model;

use Tool\Sql\SqlConnection;
use Application\Model\Helper\PageHelper;

class FileModel extends PageHelper
{
    public function __construct($onePageDisplay = 10)
    {
        parent::__construct($onePageDisplay);
    }
    
    public function getDownloadNameByFileName($fileName)
    {
        $sqlConnection = new SqlConnection();
        $param = array();

        $param[] = $fileName;
        $query = "SELECT `download_name` FROM `article_download` ";
        $query.= "WHERE `file_name` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);

        return (string)$result["download_name"];
    }
    
    public function listDownloadByTitle($title)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = "%" . $title . "%";
        $query = "SELECT A.`title`, AD.`download_name`, AD.`file_name` FROM `article_download` AD ";
        $query.= "JOIN `article` A ON A.`id` = AD.`article_id` AND A.`title` LIKE ? ";
        
        $query.= "ORDER BY AD.`id` DESC ";
        // $query.= "LIMIT 10";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listDownloadByTypeIdAndTerm($page, $typeId, $term)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        if (isset($typeId) && $typeId != "") {
            $param[] = $typeId;
        }
        
        $query = "SELECT A.`title`, AD.`download_name`, AD.`file_name` FROM `article_download` AD ";
        $query.= "JOIN `article` A ON " . ((isset($typeId) && $typeId != "") ? "A.`type_id` = ? AND " : "") . "A.`id` = AD.`article_id` ";
        
        if (isset($term) && $term != "") {
            $param[] = "%" . $term . "%";
            $query.= "WHERE AD.`download_name` LIKE ? ";
        }
        
        $query.= "ORDER BY AD.`id` DESC ";
        $query.= $this->getPageQueryString($page);
        
        return array(
            $this->getQueryDataQuantity($query, $param),
            $sqlConnection->executeQuery($query, $param)
        );
    }
}