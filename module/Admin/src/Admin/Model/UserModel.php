<?php

namespace Admin\Model;

use Admin\Model\Helper\PageHelper;
use Tool\Sql\SqlConnection;

class UserModel extends PageHelper
{
    public function __construct($onePageDisplay = 20)
    {
        parent::__construct($onePageDisplay);
    }
    
    public function addUser($username, $name, $email, $password, $roleId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = str_replace(" ", "", $username);
        $param[] = $name;
        $param[] = $email;
        $param[] = sha1(md5($password));
        $param[] = $roleId;
        
        $query = "INSERT INTO `user` (`username`, `name`, `email`, `password`, `role_id`) ";
        $query.= "VALUES (?, ?, ?, ?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteUserById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `user` WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function existsUsername($username)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $username;
        $query = "SELECT `id` FROM `user` ";
        $query.= "WHERE `username` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        if ($result != false) {
            return true;
        }
        
        return false;
    }
    
    public function getUserById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `username`, `name`, `email`, `role_id` FROM `user` ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    // 取得使用者資訊陣列, 可以使用帳號(username), 使用者姓名(name), 使用者等級(role)進行查詢
    public function listUser($currentPage, $term = null, $role = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $condition = array();
        
        $query = "SELECT U.`id`, U.`username`, U.`name`, U.`email`, R.`zh_TW` FROM `user` U ";
        $query.= "INNER JOIN `user_role` R ON R.`id` = U.`role_id` ";
        
        if (isset($role) && $role != "") {
            $param[] = $role;
            $query.= "AND R.`id` = ? ";
        }
        
        if (isset($term) && $term != "") {
            $param[] = $term . "%";
            $param[] = $term . "%";
            $condition[] = "(U.`username` LIKE ? OR U.`name` LIKE ?)";
        }
        
        $query.= (count($condition) > 0) ? "WHERE " . join(" AND ", $condition) . " " : "";
        $query.= "ORDER BY U.`id` ";
        $query.= $this->getPageQueryString($currentPage);
        
        return array(
            $this->getQueryDataQuantity($query, $param),
            $sqlConnection->executeQuery($query, $param),
        );
    }
    
    public function listRole()
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT `id`, `zh_TW` FROM `user_role`";
        return $sqlConnection->executeQuery($query);
    }
    
    public function setUserById($id, $name = null, $email = null, $password = null, $roleId = null)
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
        if (isset($roleId) && $roleId != "") {
            $param[] = $roleId;
            $condition[] = "`role_id` = ?";
        }
        
        $query.= (count($condition) > 0) ? "SET " . join(", ", $condition) . " " : "";
        $param[] = $id;
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
}