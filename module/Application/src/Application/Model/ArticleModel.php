<?php
namespace Application\Model;

use Tool\Sql\SqlConnection;
use Application\Model\Helper\PageHelper;

class ArticleModel extends PageHelper
{
    public function __construct($onePageDisplay = 20)
    {
        parent::__construct($onePageDisplay);
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
    
    public function listArticle($currentPage, $languageId = 0, $term = null, $typeId = null, $isVisible = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $condition = array();
    
        $query = "SELECT A.`id`, A.`title`, A.`content`, A.`visible`, A.`top`, A.`create_time`, AT.`type_name` ";
        $query.= "FROM `article` A ";
        $query.= "INNER JOIN `article_type` AT ON A.`type_id` = AT.`id` AND AT.`language_id` = ? ";
    
        $param[] = $languageId;
        
        if (isset($term) && $term != "") {
            $param[] = "%" . $term . "%";
            $param[] = "%" . $term . "%";
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
    
    public function listArticleImageByArticleId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $id;
        $query = "SELECT `id`, `image_name` FROM `article_image` WHERE `article_id` = ?";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listArticleDownload($quantity)
    {
        $sqlConnection = new SqlConnection();
        
        $query = "SELECT `id`, `file_name`, `download_name` FROM `article_download` ";
        $query.= "ORDER BY `id` DESC ";
        $query.= "LIMIT 0, " . $quantity;
        return $sqlConnection->executeQuery($query);
    }
    
    public function listArticleDownloadByArticleId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $id;
        $query = "SELECT `id`, `file_name`, `download_name` FROM `article_download` WHERE `article_id` = ?";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listType($languageId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $languageId;
        $query = "SELECT `id`, `type_name` FROM `article_type` WHERE `language_id` = ?";
        return $sqlConnection->executeQuery($query, $param);
    }
}

?>