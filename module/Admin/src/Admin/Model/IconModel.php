<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class IconModel
{
    public function add($params)
    {
        $sqlConnection = new SqlConnection();
        $query = "INSERT INTO `icon`(`name`) VALUES (?)";

        $values = array();
        $values[] = $params["name"];

        return $sqlConnection->executeQuery($query, $values);
    }

    public function all()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT * FROM `icon` ORDER BY `id`";

        return $sqlConnection->executeQuery($query);
    }

    public function destroy($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "DELETE FROM `icon` WHERE `id` = ?";
        $conditions = array();

        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions);
    }

    public function find($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT * FROM `icon` WHERE `id` = ?";
        $conditions = array();
        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions, true);
    }

    public function update($params)
    {
        $sqlConnection = new SqlConnection();
        $query = "UPDATE `icon` SET `name` = ? WHERE `id` = ?";
        $values = array();

        $values[] = $params["name"];
        $values[] = $params["id"];

        return $sqlConnection->executeQuery($query, $values);
    }
}
?>