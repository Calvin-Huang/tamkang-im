<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;
use Admin\Model\Helper\PageHelper;

class ArticleModel extends PageHelper
{
    public function __construct($onePageDisplay = 20)
    {
        parent::__construct($onePageDisplay);
    }
    
    public function addArticle($title, $content, $typeId, $top)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $title;
        $param[] = $content;
        $param[] = $typeId;
        $param[] = 1;
        $param[] = $top;
        $param[] = date("Y-m-d H:i:s");
        $param[] = date("Y-m-d H:i:s");
        $query = "INSERT INTO `article` (`title`, `content`, `type_id`, `visible`, `top`, `create_time`, `update_time`) ";
        $query.= "VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function addArticleImage($articleId, $imageName)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $articleId;
        $param[] = $imageName;
        $query = "INSERT INTO `article_image` (`article_id`, `image_name`) ";
        $query.= "VALUES (?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function addArticleDownload($articleId, $fileName, $downloadName)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $articleId;
        $param[] = $fileName;
        $param[] = $downloadName;

        $query = "INSERT INTO `article_download` (`article_id`, `file_name`, `download_name`) ";
        $query.= "VALUES (?, ?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function addType($params)
    {
        if (isset($params["name"]) && $params["name"] != "") {
            $sqlConnection = new SqlConnection();
            $query = "INSERT INTO `article_type`(`type_name`, `language_id`, `sort`) VALUES (?, ?, ?)";
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

        $query = "UPDATE `article_type` SET `sort` = `sort` - 1 WHERE `sort` > ?";
        $conditions[] = $this->getSortFromTypeById($id);

        $sqlConnection->executeQuery($query, $conditions);

        $conditions = array();

        $query = "DELETE FROM `article_type` WHERE `id` = ?";
        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions);
    }

    public function deleteArticleById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $id;
        $query = "DELETE FROM `article` WHERE `id` = ?";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteArticletype()
    {
        $sqlConnection = new SqlConnection();
        $query = "DELETE FROM `article_type`";
    
        return $sqlConnection->executeQuery($query);
    }
    
    public function deleteArticleImageById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $id;
        $query = "DELETE FROM `article_image` WHERE `id` = ?";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteArticleDownloadById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $id;
        $query = "DELETE FROM `article_download` WHERE `id` = ?";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteArticleImageByArticleId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `article_image` WHERE `article_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteArticleDownloadByArticleId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `article_download` WHERE `article_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public  function exists($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `id` FROM `article_type` WHERE `id` = ?";
        $param[] = $id;

        return $sqlConnection->executeQuery($query, $param, true) != null;
    }

    public function getSortFromTypeById($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `sort` FROM `article_type` WHERE `id` = ?";
        $conditions[] = $id;

        $result = $sqlConnection->executeQuery($query, $conditions, true);

        return $result["sort"];
    }

    public function getLastSortFromType()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `sort` FROM `article_type` ORDER BY `sort` DESC LIMIT 1";
        $result = $sqlConnection->executeQuery($query, null, true);

        return (int)$result["sort"];
    }

    public function getArticleById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `title`, `content`, `type_id`, `visible`, `create_time`, `top` ";
        $query.= "FROM `article`";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function getArticleByIdAndIsVisible($id, $isVisible = true)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $id;
        $param[] = $isVisible;
        $query = "SELECT `title`, `content`, `type_id`, `create_time` ";
        $query.= "FROM `article`";
        $query.= "WHERE `id` = ? AND `visible` = ?";
    
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function getArticleImageById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `image_name` FROM `article_image` WHERE `id` = ?";
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["image_name"];
    }
    
    public function getArticleDownloadById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `file_name`, `download_name` FROM `article_download` WHERE `id` = ?";
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["file_name"];
    }
    
    public function listArticle($currentPage, $term = null, $typeId = null, $isVisible = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $condition = array();
        
        $query = "SELECT A.`id`, A.`title`, A.`content`, A.`visible`, A.`top`, A.`create_time`, AT.`type_name` ";
        $query.= "FROM `article` A ";
        $query.= "INNER JOIN `article_type` AT ON A.`type_id` = AT.`id` ";
        
        if (isset($term) && $term != "") {
            $param[] = $term . "%";
            $param[] = $term . "%";
            $condition[] = "(`title` LIKE ? OR `content` LIKE ?)";
        }
        
        if (isset($typeId) && $typeId != "") {
            $param[] = $typeId;
            $condition[] = "`type_id` = ?";
        }
        
        if (isset($isVisible) && $isVisible != "") {
            $param[] = $isVisible;
            $condition[] = "`visible` = ?";
        }
        
        $query.= (count($condition) > 0) ? "WHERE " . join(" AND ", $condition) . " " : "";
        $query.= "ORDER BY `top` DESC, `create_time` DESC ";
        $query.= $this->getPageQueryString($currentPage);
        
        return array(
            $this->getQueryDataQuantity($query, $param),
            $sqlConnection->executeQuery($query, $param),
        );
    }
    
    public function listType()
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT AT.`id`, AT.`type_name`, L.`language`, AT.`language_id`, AT.`sort` FROM `article_type` AT ";
        $query.= "LEFT JOIN `language` L ON (AT.`language_id` = L.`id`) ";
        $query.= "ORDER BY AT.`sort`";
        return $sqlConnection->executeQuery($query);
    }
    
    public function listArticleImageByArticleId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `id`, `image_name` FROM `article_image` WHERE `article_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listArticleDownloadByArticleId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `id`, `file_name`, `download_name` FROM `article_download` WHERE `article_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setArticletype($types, $languages)
    {
        $sqlConnection = new SqlConnection();
        $this->deleteArticletype();
        
        $query = "ALTER TABLE `article_type` AUTO_INCREMENT = 1";
        $sqlConnection->executeQuery($query);
        
        $query = "INSERT INTO `article_type` (`type_name`, `language_id`) ";
        $query.= "VALUES (?, ?)";
        
        foreach ($types as $key => $value) {
            if ($value != "" && $languages[$key] != "") {
                $param = array();
                
                $param[] = $value;
                $param[] = $languages[$key];
                $sqlConnection->executeQuery($query, $param);
            }
        }
        
        return true;
    }
    
    public function updateArticleById($id, $title, $content, $typeId, $visible = 1, $top = 0)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $condition = array();
        
        $query = "UPDATE `article` SET ";
        
        $param[] = $title;
        $condition[] = "`title` = ?";
        
        $param[] = $content;
        $condition[] = "`content` = ?";
        
        $param[] = $typeId;
        $condition[] = "`type_id` = ?";
        
        $param[] = $visible;
        $condition[] = "`visible` = ?";
        
        $param[] = $top;
        $condition[] = "`top` = ?";
        
        $param[] = date("Y-m-d H:i:s");
        $condition[] = "`update_time` = ?";
        
        $query.= join(", ", $condition) . " ";
        
        $param[] = $id;
        $query.= "WHERE `id` = ?";

        return $sqlConnection->executeQuery($query, $param);
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
        $query = "UPDATE `article_type` SET `type_name` = ?, `language_id` = ?, `sort` = ?  WHERE `id` = ?";

        $conditions[] = $params["name"];
        $conditions[] = $params["language"];
        $conditions[] = $params["sort"];
        $conditions[] = $params["id"];

        return $sqlConnection->executeQuery($query, $conditions, true);
    }
}