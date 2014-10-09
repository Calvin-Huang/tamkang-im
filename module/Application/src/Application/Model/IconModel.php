<?php

namespace Application\Model;

use Tool\Sql\SqlConnection;

class IconModel
{
    public function find($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT * FROM `icon` WHERE `id` = ?";
        $conditions = array();
        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions, true);
    }
}
?>