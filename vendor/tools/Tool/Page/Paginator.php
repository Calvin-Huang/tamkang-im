<?php

namespace Tool\Page;

class Paginator
{
    public static function factory($currentPage, $onePageQuantity, $totalDataQuantity)
    {
        if (!isset($_SERVER["REQUEST_URL"])) {
            $url = "";
        } else {
            $url = $_SERVER["REQUEST_URL"];
        }
        
        $previousQuery = preg_replace("/\\&?page=\\d\\&?/", "", $_SERVER["QUERY_STRING"]);
        
        if (isset($previousQuery) && $previousQuery != "") {
            $previousQuery = "&" . $previousQuery;
        }
        
        $max = round($totalDataQuantity / $onePageQuantity + 0.49);
        $max = ($max == 0) ? 1 : $max;
        $range = 4;
        $paginator = "";
        $maxRange = $range * 2 + 1;
        
        // 防止超出範圍
        if ($currentPage > $max) {
            $currentPage = $max;
        }
        
        // 防止小於範圍
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        
        // 1~N~*
        if ($currentPage - 1 < $range && $max > $maxRange) {
            for ($i = 0; $i < $maxRange; $i++) {
                if ($currentPage - 1 == $i) {
                    $paginator .= "<li class=\"active\"><a href=\"#\">" . ($i + 1) . "</a></li>\n";
                } else {
                    $paginator .= "<li><a href=\"" . $url . "?page=" . ($i + 1) . $previousQuery . "\">" . ($i + 1) . "</a></li>\n";
                }
            }
            
        // *~N~Max
        } else if ($currentPage > $max - $range && $max > $maxRange) {
            for ($i = $max - $maxRange; $i < $max; $i++) {
                if ($currentPage - 1 == $i) {
                    $paginator .= "<li class=\"active\"><a href=\"#\">" . ($i + 1) . "</a></li>\n";
                } else {
                    $paginator .= "<li><a href=\"" . $url . "?page=" . ($i + 1) . $previousQuery . "\">" . ($i + 1) . "</a></li>\n";
                }
            }
        
        // *~N~**
        } else  if($max > $maxRange) {
            for ($i = $currentPage - $range - 1; $i < $currentPage + $range; $i++) {
                if ($currentPage - 1 == $i) {
                    $paginator .= "<li class=\"active\"><a href=\"#\">" . ($i + 1) . "</a></li>\n";
                } else {
                    $paginator .= "<li><a href=\"" . $url . "?page=" . ($i + 1) . $previousQuery . "\">" . ($i + 1) . "</a></li>\n";
                }
            }
        } else {
            for ($i = 0; $i < $max; $i++) {
                if ($currentPage - 1 == $i) {
                    $paginator .= "<li class=\"active\"><a href=\"#\">" . ($i + 1) . "</a></li>\n";
                } else {
                    $paginator .= "<li><a href=\"" . $url . "?page=" . ($i + 1) . $previousQuery . "\">" . ($i + 1) . "</a></li>\n";
                }
            }
        }
        
        if ($currentPage != 1) {
            
            // 上一頁
            $paginator = "<li><a href=\"" . $url . "?page=" . ($currentPage - 1) . $previousQuery . "\">&lsaquo;</a></li>\n" . $paginator;
            
            // 最前頁
            $paginator = "<li><a href=\"" . $url . "?page=1" . $previousQuery . "\">&laquo;</a></li>\n" . $paginator;
        } else {
            $paginator = "<li class=\"disabled\"><a href=\"#\">&lsaquo;</a></li>\n" . $paginator;
        }
        
        if ($currentPage != $max) {
            
            // 下一頁
            $paginator = $paginator . "<li><a href=\"" . $url . "?page=" . ($currentPage + 1) . $previousQuery . "\">&rsaquo;</a></li>\n";
            
            // 最後頁
            $paginator = $paginator . "<li><a href=\"" . $url . "?page=" . ($max) . $previousQuery . "\">&raquo;</a></li>\n";
        } else {
            $paginator = $paginator . "<li class=\"disabled\"><a href=\"#\">&rsaquo;</a></li>\n";
        }
        
        return "<ul class=\"pagination\">" . $paginator . "</ul>";
    }
}
?>