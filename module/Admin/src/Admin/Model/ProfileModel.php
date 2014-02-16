<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class ProfileModel
{
    public function updateProfileById($id, $name = null, $email = null, $password = null, $avatar = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $condition = array();
        
        $query = "UPDATE `user` ";
        
        if (isset($name) && $name != "") {
            $param[] = $name;
            $condition[] = "`name` = ?";
        }
        if (isset($email) && $email != "") {
            $param[] = $email;
            $condition[] = "`email` = ?";
        }
        if (isset($password) && $password != "") {
            $param[] = sha1(md5($password));
            $condition[] = "`password` = ?";
        }
        if (isset($avatar) && $avatar != "") {
            $param[] = $avatar;
            $condition[] = "`avatar` = ?";
        }
        
        $query.= (count($condition) > 0) ? "SET " . join(", ", $condition) . " " : "";
        
        $param[] = $id;
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function getPasswordById($id)
    {
        $sqlConnection = new SqlConnection();
        
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `password` FROM `user` WHERE `id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return (string)$result["password"];
    }
}