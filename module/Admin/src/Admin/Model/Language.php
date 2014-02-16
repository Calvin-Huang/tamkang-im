<?php
namespace Admin\Model;

use Tool\Sql\SqlConnection;

class Language
{
    public function __construct() {
        
    }
    
    public function listLanguage() {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `id`, `language`, `short_cut` FROM `language` ORDER BY `id`";
        
        return $sqlConnection->executeQuery($query);
    }
}

?>