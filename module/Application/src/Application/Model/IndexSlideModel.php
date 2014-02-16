<?php
namespace Application\Model;

use Tool\Sql\SqlConnection;

class IndexSlideModel
{
    public function listIndexSlide()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `image` ";
        $query.= "FROM `index_slide` ";
        $query.= "ORDER BY `sort`";
        
        return $sqlConnection->executeQuery($query);
    }
}
?>