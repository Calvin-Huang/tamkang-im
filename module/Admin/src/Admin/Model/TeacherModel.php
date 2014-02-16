<?php

namespace Admin\Model;

use Tool\Sql\SqlConnection;
use Tool\Curl\CurlTool;

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
    
    public function addBookByTeacherId($teacherId, $title, $typeId, $url = null)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        $columnString = "`teacher_id`, `title`, `type_id`";
        $queryString = "?, ?, ?";
        
        $param[] = $teacherId;
        $param[] = $title;
        $param[] = $typeId;
        
        if ($url != "" && isset($url)) {
            $param[] = $url;
            $columnString .= ", `url`";
            $queryString .= ", ?";
        }
        
        $query = "INSERT INTO `teacher_book` (" . $columnString . ") ";
        $query.= "VALUES (" . $queryString . ")";
    
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
    
    public function deleteBookById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `teacher_book` ";
        $query.= "WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
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
    
    public function deleteProfilebyId($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "DELETE FROM `teacher_profile` ";
        $query.= "WHERE `id` = ?";
        
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
    
    public function getBooks($config, $typeValue, $teacherName)
    {
        $curlTool = new CurlTool();
        $data = array();
    
        $contents[] = $curlTool->getCurl($config["teacher_system_url"] . "?s=" . $typeValue . "&kwd=" . urlencode($teacherName));
    
        $pageBlock = explode("<div class=\"x_pager_style1\">", $contents[0]);
    
        // 有頁數區塊才有資料
        if (count($pageBlock) > 1) {
            $pageBlock = explode("</div>", $pageBlock[1]);
            $pageBlock = $pageBlock[0];
    
            // 取得所有資料數量，一頁30筆，算出所有頁數
            $totalPages = ceil(((int)(preg_replace("/[\\s\\S]*共有 (\\d+) 筆查詢結果[\\s\\S]*/", "\${1}", $pageBlock))) / 30);
    
            // 取得所有頁面內容
            if ($totalPages > 1) {
                for ($i = 1; $i < $totalPages; $i++) {
                    $contents[] = $curlTool->getCurl($config["teacher_system_url"] . "/StaffSummary.aspx?s=" . $typeValue . "&kwd=" . urlencode($teacherName) . "&pg=" . ($i + 1));
                }
            }
    
            foreach ($contents as $i => $content) {
    
                // 以id ctl00_ContentPlaceHolder1_divCtn切割掉前面不要的區塊
                $infoBlock = explode("ctl00_ContentPlaceHolder1_divCtn", $content);
                $infoBlock = explode("<tbody>", $infoBlock[1]);
                $infoBlock = explode("</tbody>", $infoBlock[1]);
                $infoBlock = $infoBlock[0];
    
                $infoBlock = strip_tags($infoBlock, "<tr><a>");
                $infoBlock = str_replace("\n", "", $infoBlock);
                $infoBlock = str_replace("\r", "", $infoBlock);
    
                $dataRows = split("</tr>", $infoBlock);
                $temp = "";
    
                foreach ($dataRows as $i => $dataRow) {
                    $dataRow = explode("發佈", $dataRow);
    
                    if (isset($dataRow[1]) && $dataRow[1] != "") {
                        $temp = explode("href=\"", $dataRow[1]);
                        $temp = explode("\"", $temp[1]);
    
                        $data[] = array(
                                "url" => $config["teacher_system_url"] . "/" . $temp[0],
                                "info" => strip_tags($dataRow[1])
                        );
                    }
                }
            }
        }
        
        return $data;
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
    
    public function getBooktypeValueById($id)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $id;
        $query = "SELECT `type_value` FROM `teacher_book_type` ";
        $query.= "WHERE `id` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return (string)$result["type_value"];
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
        
        $query = "SELECT U.`name`, TN.`title_name`, T.`title_id`, T.`id` FROM `teacher` T ";
        $query.= "INNER JOIN `user` U ON U.`id` = T.`user_id` ";
        $query.= "INNER JOIN `teacher_title` TN ON TN.`id` = T.`title_id` ";
        $query.= "ORDER BY T.`sort`";
        
        return $sqlConnection->executeQuery($query);
    }
    
    public function listBookType()
    {
        $sqlConnection = new SqlConnection();
    
        $query = "SELECT `id`, `type_name`, `type_value`, `type_en_US` FROM `teacher_book_type`";
        return $sqlConnection->executeQuery($query);
    }
    
    public function listBookByTeacherIdAndTypeId($teacherId, $typeId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $typeId;
        $param[] = $teacherId;
        $query = "SELECT B.`title`, B.`url`, B.`id` ";
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
        $query = "SELECT P.`id`, P.`profile` AS `title` ";
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
    
    public function setTeacherTitleById($id, $titleId)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $titleId;
        $param[] = $id;
        
        $query = "UPDATE `teacher` SET `title_id` = ? WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setBookById($id, $title)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $param[] = $title;
        $param[] = $id;
        
        $query = "UPDATE `teacher_book` SET `title` = ? WHERE `id` = ?";
        
        return $sqlConnection->executeQuery($query, $param);
    }
    
    public function setBooktype($types, $type_en_US, $values)
    {
        $sqlConnection = new SqlConnection();
        $this->deleteBooktype();
        
        $query = "ALTER TABLE `teacher_book_type` AUTO_INCREMENT = 1";
        $sqlConnection->executeQuery($query);
        
        $query = "INSERT INTO `teacher_book_type` (`type_name`, `type_en_US`, `type_value`) ";
        $query.= "VALUES (?, ?, ?) ";
        foreach ($types as $key => $value) {
            if ($value != "") {
                $param = array();
                
                $param[] = $value;
                $param[] = $type_en_US[$key];
                $param[] = $values[$key];
                $sqlConnection->executeQuery($query, $param);
            }
        }
        
        return true;
    }
    
    public function setProfileById($id, $title)
    {
        $sqlConnection = new SqlConnection();
        $param = array();
    
        $param[] = $title;
        $param[] = $id;
    
        $query = "UPDATE `teacher_profile` SET `profile` = ? WHERE `id` = ?";
    
        return $sqlConnection->executeQuery($query, $param);
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