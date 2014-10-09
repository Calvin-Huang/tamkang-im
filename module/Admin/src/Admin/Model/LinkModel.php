<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class LinkModel
{
    public function __construct()
    {

    }

    public function add($params)
    {
        $sqlConnection = new SqlConnection();
        $query = "INSERT INTO `link`(`link_type_id`, `name`, `url`) VALUES (?, ?, ?)";
        $values = array();
        $values[] = $params["link_type_id"];
        $values[] = $params["name"];
        $values[] = $params["url"];

        return $sqlConnection->executeQuery($query, $values);
    }

    public function destroy($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "DELETE FROM `link` WHERE `id` = ?";
        $conditions = array();
        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions);
    }

    public function find($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT * FROM `link` WHERE `id` = ?";
        $conditions = array();
        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions, true);
    }

    public function findByLinkTypeId($linkTypeId)
    {
        $sqlConnection = new SqlConnection();

        $query = "SELECT * FROM `link` WHERE `link_type_id` = ? ORDER BY `id`";
        $conditions = array();

        $conditions[] = $linkTypeId;

        return $sqlConnection->executeQuery($query, $conditions);
    }

    public function update($params)
    {
        $sqlConnection = new SqlConnection();
        $query = "UPDATE `link` SET `link_type_id` = ?, `name` = ?, `url` = ? WHERE `id` = ?";
        $values = array();
        $values[] = $params["link_type_id"];
        $values[] = $params["name"];
        $values[] = $params["url"];
        $values[] = $params["id"];

        $sqlConnection->executeQuery($query, $values);

        return (int)$params["id"];
    }
}
?>