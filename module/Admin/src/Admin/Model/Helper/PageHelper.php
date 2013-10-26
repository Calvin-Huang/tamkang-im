<?php

namespace Admin\Model\Helper;

use Tool\Sql\SqlConnection;

class PageHelper
{
    private $onePageDisplay = 0;
    
    public function __construct($onePageDisplay = 10)
    {
        $this->onePageDisplay = (int)$onePageDisplay;
    }
    
    public function getPageQueryString($currentPageNum)
    {
        $start = ((int)$currentPageNum - 1) * $this->onePageDisplay;
        return "LIMIT " . $start . ", " . $this->onePageDisplay;
    }
    
    public function getQueryDataQuantity($query, $param)
    {
        $sqlConnection = new SqlConnection();
        
        $query = preg_replace("/\\sLIMIT.*/i", "", $query);
        $query = preg_replace("/\\sORDER.*/i", "", $query);
        $query = preg_replace("/SELECT\\s(.*)\\sFROM(.*)/i", "SELECT COUNT(*) AS `num` FROM\${2}", $query);
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        return (int)$result["num"];
    }
}