<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;

class TeacherModel
{
    public function __construct()
    {
    }
    
    public function addTeacherByUserId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        
        // 預設教授頭銜為：1 -> 專任教授
        $param[] = 1;
        $param[] = $this->countTeacher() + 1;
        $query = "INSERT INTO `teacher` (`user_id`, `title_id`, `sort`) ";
        $query.= "VALUES (?, ?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function addBookByTeacherId($teacherId, $title, $typeId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $teacherId;
        $param[] = $title;
        $param[] = $typeId;
        $query = "INSERT INTO `teacher_book` (`teacher_id`, `title`, type_id) ";
        $query.= "VALUES (?, ?, ?)";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function addProfileByTeacherId($teacherId, $profile, $typeId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $teacherId;
        $param[] = $profile;
        $param[] = $typeId;
        $query = "INSERT INTO `teacher_profile` (`teacher_id`, `profile`, `type_id`) ";
        $query.= "VALUES (?, ?, ?)";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function countTeacher()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT COUNT(*) AS `total` FROM `teacher`";
        $result = $sqlConnection->executeQuery($query, array(), true);
        
        return (int)$result["total"];
    }
    
    public function deleteTeacherByUserId($id)
    {
        $sqlConnection = new SqlConnection();
        
        $sort = $this->getTeacherSortByUserId($id);
        $param = array();
        
        $param[] = $sort;
        $query = "UPDATE `teacher` SET `sort` = `sort` -1 WHERE `sort` >= ?";
        $sqlConnection->executeQuery($query, $param);
        
        $param = array();
        
        $param[] = $id;
        $query = "DELETE `T`, `TB`, `TO`, `TP` FROM `teacher` `T` ";
        $query.=     "LEFT JOIN `teacher_book` `TB` ON (`T`.`id` = `TB`.`teacher_id`) ";
        $query.=     "LEFT JOIN `teacher_othertitle` `TO` ON (`T`.`id` = `TO`.`teacher_id`) ";
        $query.=     "LEFT JOIN `teacher_profile` `TP` ON (`T`.`id` = `TP`.`teacher_id`) ";
        $query.= "WHERE `T`.`user_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteBooktype()
    {
        $sqlConnection = new SqlConnection();
        
        $query = "DELETE FROM `teacher_book_type`";
        return $sqlConnection->executeQuery($query);
    }
    
    public function deleteBookByTeacherIdAndTypeId($teacherId, $typeId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $teacherId;
        $param[] = $typeId;
        $query = "DELETE FROM `teacher_book` ";
        $query.= "WHERE `teacher_id` = ? AND `type_id` = ?";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteOthertitleByTeacherId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `teacher_othertitle` ";
        $query.= "WHERE `teacher_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function deleteProfileByTeacherIdAndTypeId($teacherId, $typeId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $teacherId;
        $param[] = $typeId;
        $query = "DELETE FROM `teacher_profile` ";
        $query.= "WHERE `teacher_id` = ? AND `type_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function getBooktypeNameById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `type_name` FROM `teacher_book_type` ";
        $query.= "WHERE `id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["type_name"];
    }
    
    public function getProfileNameById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `type_name` FROM `teacher_profile_type` ";
        $query.= "WHERE `id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["type_name"];
    }
    
    public function getTeacherTitleIdById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `title_id` FROm `teacher` ";
        $query.= "WHERE `id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["title_id"];
    }
    
    public function getTeacherTitleIdByUserId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `title_id` FROM `teacher` ";
        $query.= "WHERE `user_id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["title_id"];
    }
    
    public function getTeacherSortByUserId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `sort` FROM `teacher` ";
        $query.= "WHERE `user_id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["sort"];
    }
    
    public function getTeacherIdByUserId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `id` FROM `teacher` ";
        $query.= "WHERE `user_id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["id"];
    }
    
    public function getTeacherById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `id`, `user_id`, `teach_class`, `lab_location`, `labsite_url`, ";
        $query.=     "`labsite_text`, `personalsite_url`, `personalsite_text`";
        $query.= "FROM `teacher`";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function getTeacherByUserId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT T.`id`, T.`user_id`, T.`teach_class`, T.`lab_location`, T.`labsite_url`, ";
        $query.=     "T.`labsite_text`, T.`personalsite_url`, T.`personalsite_text`, TI.`title_name`";
        $query.= "FROM `teacher` T ";
        $query.= "INNER JOIN `teacher_title` TI ON (T.`title_id` = TI.`id`) ";
        $query.= "WHERE T.`user_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function listTeacher()
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT U.`name`, TN.`title_name`, T.`id` FROM `teacher` T ";
        $query.= "INNER JOIN `user` U ON U.`id` = T.`user_id` ";
        $query.= "INNER JOIN `teacher_title` TN ON TN.`id` = T.`title_id` ";
        $query.= "ORDER BY T.`sort`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listBookType()
    {
        $sqlConnection = new SqlConnection();
    
        $query = "SELECT `id`, `type_name` FROM `teacher_book_type`";
        return $sqlConnection->executeQuery($query);
    }
    
    public function listBookByTeacherIdAndTypeId($teacherId, $typeId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $typeId;
        $param[] = $teacherId;
        $query = "SELECT B.`title`, B.`id` ";
        $query.= "FROM `teacher_book` B ";
        $query.= "INNER JOIN `teacher_book_type` BT ON B.`type_id` = BT.`id` AND BT.`id` = ? ";
        $query.= "WHERE B.`teacher_id` = ? ";
        $query.= "ORDER BY B.`type_id`";
    
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listProfileType()
    {
        $sqlConnection = new SqlConnection();
        
        $query = "SELECT `id`, `type_name` FROM `teacher_profile_type`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listProfileByTeacherIdAndTypeId($teacherId, $typeId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $typeId;
        $param[] = $teacherId;
        $query = "SELECT P.`id`, P.`profile` ";
        $query.= "FROM `teacher_profile` P ";
        $query.= "INNER JOIN `teacher_profile_type` PT ON P.`type_id` = PT.`id` AND PT.`id` = ? ";
        $query.= "WHERE P.`teacher_id` = ? ";
        $query.= "ORDER BY P.`type_id`";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function listTitle()
    {
        $sqlConnection = new SqlConnection();
        
        $query = "SELECT `id`, `title_name` ";
        $query.= "FROM `teacher_title`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listOthertitleByTeacherId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `title_name` FROM `teacher_othertitle` ";
        $query.= "WHERE `teacher_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setSort($sort)
    {
        $sqlConnection = new SqlConnection();
        $query = "UPDATE `teacher` SET `sort` = ? WHERE `id` = ?";
        
        foreach ($sort as $key => $value) {
            $param = array();
            $param[] = $key + 1;
            $param[] = $value;
            $sqlConnection->executeQuery($query, $param);
        }
        
        return true;
    }
    
    public function setBooktype($types)
    {
        $sqlConnection = new SqlConnection();
        $this->deleteBooktype();
        
        $query = "ALTER TABLE `teacher_book_type` AUTO_INCREMENT = 1";
        $sqlConnection->executeQuery($query);
        
        $query = "INSERT INTO `teacher_book_type` (`type_name`) ";
        $query.= "VALUES (?) ";
        foreach ($types as $key => $value) {
            if ($value != "") {
                $param = array();
                
                $param[] = $value;
                $sqlConnection->executeQuery($query, $param);
            }
        }
        
        return true;
    }
    
    public function setTeacherById($id)
    {
        
    }
    
    public function setTeacherByUserId($id, $titleId = null, $teachClass = null, 
        $labLocation = null, $labsiteText = null, $labsiteUrl = null, $personalsiteText = null, $personalsiteUrl = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $condition = array();
        
        $query = "UPDATE `teacher` ";
        
        if (isset($titleId) && $titleId != "") {
            $param[] = $titleId;
            $condition[] = "`title_id` = ?";
        }
        
        if (isset($teachClass)) {
            $param[] = $teachClass;
            $condition[] = "`teach_class` = ?";
        }
        
        if (isset($labLocation)) {
            $param[] = $labLocation;
            $condition[] = "`lab_location` = ?";
        }
        
        if (isset($labsiteText)) {
            $param[] = $labsiteText;
            $condition[] = "`labsite_text` = ?";
        }
        
        if (isset($labsiteUrl)) {
            $param[] = $labsiteUrl;
            $condition[] = "`labsite_url` = ?";
        }
        
        if (isset($personalsiteText)) {
            $param[] = $personalsiteText;
            $condition[] = "`personalsite_text` = ?";
        }
        
        if (isset($personalsiteUrl)) {
            $param[] = $personalsiteUrl;
            $condition[] = "`personalsite_url` = ?";
        }
        
        $query.= (count($condition) > 0) ? "SET " . join(", ", $condition) . " " : "";
        
        $param[] = $id;
        $query.= "WHERE `user_id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setOthertitleByTeacherId($id, $othertitles)
    {
        $this->deleteOthertitleByTeacherId($id);
        $sqlConnection = new SqlConnection();
        
        // $query = "ALTER TABLE `teacher_othertitle` AUTO_INCREMENT = 1";
        // $sqlConnection->executeQuery($query);
        
        $query = "INSERT INTO `teacher_othertitle` (`teacher_id`, `title_name`) ";
        $query.= "VALUES (?, ?)";
        
        foreach ($othertitles as $i => $othertitle) {
            if ($othertitle != "") {
                $param = array();
                
                $param[] = $id;
                $param[] = $othertitle;
                $sqlConnection->executeQuery($query, $param);
            }
        }
        
        return true;
    }
}