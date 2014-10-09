<?php

namespace Application\Model;

use Tool\Sql\SqlConnection;

class LinkModel
{
    public function findByLinkTypeId($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT * FROM `link` WHERE `link_type_id` = ?";
        $conditions = array();
        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions);
    }
}
?>