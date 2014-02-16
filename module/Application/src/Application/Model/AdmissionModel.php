<?php

namespace Application\Model;

use Tool\Sql\SqlConnection;

class AdmissionModel
{
    public function __construct()
    {
        
    }
    
    public function getAdmission()
    {
        $sqlConnection = new SqlConnection();
        $query = "SELECT `content` FROM `admission` LIMIT 1";
        
        return $sqlConnection->executeQuery($query, null, true);
    }
}
?>