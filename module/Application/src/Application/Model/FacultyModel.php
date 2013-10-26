<?php
namespace Application\Model;

use Application\Model\Helper\PageHelper;
use Tool\Sql\SqlConnection;

class FacultyModel extends PageHelper
{
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
    
    public function listTitle()
    {
        $sqlConnection = new SqlConnection();
    
        $query = "SELECT `id`, `title_name` ";
        $query.= "FROM `teacher_title`";
    
        return $sqlConnection->executeQuery($query);
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
    
    public function getTeacherById($id)
    {
        $sqlConnection = new SqlConnection();
        
        $param[] = $id;
        $query = "SELECT U.`name`, TN.`title_name`, U.`avatar`, U.`email`, T.`id`, T.`lab_location`, T.`teach_class`, ";
        $query.=     "T.`labsite_url`, T.`labsite_text`, T.`personalsite_url`, T.`personalsite_text` FROM `teacher` T ";
        $query.= "INNER JOIN `user` U ON U.`id` = T.`user_id` ";
        $query.= "INNER JOIN `teacher_title` TN ON TN.`id` = T.`title_id` ";
        $query.= "WHERE T.`id` = ?";
        
        return $sqlConnection->executeQuery($query, $param, true);
    }
    
    public function listTeacherByPage($page = 1)
    {
        $this->setOnePageDisplay(20);
        
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $query = "SELECT U.`name`, TN.`title_name`, U.`avatar`, U.`email`, T.`id`, T.`lab_location` FROM `teacher` T ";
        $query.= "INNER JOIN `user` U ON U.`id` = T.`user_id` ";
        $query.= "INNER JOIN `teacher_title` TN ON TN.`id` = T.`title_id` ";
        $query.= "ORDER BY T.`sort`";
        $query.= $this->getPageQueryString($page);

        return array(
                $this->getQueryDataQuantity($query, $param),
                $sqlConnection->executeQuery($query, $param),
        );
    }
    
    public function listTeacherByTitleIdAndPage($titleId = null, $page)
    {
        $this->setOnePageDisplay(20);
        
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT U.`name`, TN.`title_name`, U.`avatar`, U.`email`, T.`id`, T.`lab_location` FROM `teacher` T ";
        $query.= "INNER JOIN `user` U ON U.`id` = T.`user_id` ";
        $query.= "INNER JOIN `teacher_title` TN ON TN.`id` = T.`title_id` ";
        
        if ($titleId != null) {
            $param[] = $titleId;
            $query.= " AND T.`title_id` = ? ";
        }
        
        $query.= "ORDER BY T.`sort`";
        $query.= $this->getPageQueryString($page);
        
        return array(
                $this->getQueryDataQuantity($query, $param),
                $sqlConnection->executeQuery($query, $param),
        );
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
}
?>