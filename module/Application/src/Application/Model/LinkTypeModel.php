<?php

namespace Application\Model;

use Tool\Sql\SqlConnection;

class LinkTypeModel
{
    public function all()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT * FROM `link_type` ORDER BY `sort`";

        return $sqlConnection->executeQuery($query);
    }
}
?>