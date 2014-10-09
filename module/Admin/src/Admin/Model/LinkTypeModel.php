<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class LinkTypeModel
{
    public function __construct()
    {

    }

    public function add($params)
    {
        if (isset($params["name"]) && $params["name"] != "") {
            $sqlConnection = new SqlConnection();
            $query = "INSERT INTO `link_type`(`icon_id`, `name`, `sort`, `language_id`) VALUES(?, ?, ?, ?)";
            $values = array();
            $values[] = $params["icon_id"];
            $values[] = $params["name"];
            $values[] = $this->lastSort() + 1;
            $values[] = $params["language"];

            return $sqlConnection->executeQuery($query, $values);
        } else {
            return false;
        }
    }

    public function all()
    {
        $query = "SELECT * FROM `link_type` ORDER BY `sort`";
        $sqlConnection = new SqlConnection();

        return $sqlConnection->executeQuery($query);
    }

    public function delete($id)
    {
        $sqlConnection = new SqlConnection();
        $condition = array();

        $query = "DELETE FROM `link_type` WHERE `id` = ?";
        $condition[] = $id;

        return $sqlConnection->executeQuery($query, $condition);
    }

    public function exists($id)
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `id` FROM `link_type` WHERE `id` = ?";
        $conditions = array();

        $conditions[] = $id;

        return $sqlConnection->executeQuery($query, $conditions, true) != null;
    }

    public function lastSort()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `sort` FROM `link_type` LIMIT 1";

        $result = $sqlConnection->executeQuery($query, null, true);

        return (int)$result["sort"];
    }

    public function update($params)
    {
        $sqlConnection = new SqlConnection();
        $query = "UPDATE `link_type` SET `icon_id` = ?, `name` = ?, `sort` = ?, `language_id` = ? WHERE `id` = ?";
        $values = array();

        $values[] = $params["icon_id"];
        $values[] = $params["name"];
        $values[] = $params["sort"];
        $values[] = $params["language"];
        $values[] = $params["id"];

        return $sqlConnection->executeQuery($query, $values, true);
    }

    public function updateAll($params)
    {
        $sqlConnection = new SqlConnection();

        foreach ($params["type"] as $k => $v) {
            if ($this->exists($v["id"])) {
                if ($v["_delete"]) {
                    $this->delete($v["id"]);
                } else {
                    $this->update($v);
                }
            } else {
                $this->add($v);
            }
        }

        return true;
    }
}
?>