<?php
namespace Application\Model;

use Tool\Sql\SqlConnection;

class Language
{
    public function __construct() {
        
    }
    
    public function getLanguageIdByShortCut($shortCut) {
        $sqlConnection = new SqlConnection();
        $param = array();
        
        $query = "SELECT `id` FROM `language` WHERE `short_cut` LIKE ?";
        $param[] = "%" . $shortCut . "%";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return $result["id"];
    }
}

?>