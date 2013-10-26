<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response\Stream;

class ImageController extends AbstractActionController
{
    public function indexAction()
    {
        $fileName = $_SERVER["QUERY_STRING"];
        
        $fileInfo = new \finfo();
        $image = glob(realpath(__DIR__ . "/../../../../../data/upload") . "/" . $fileName . ".{jpg,png,gif}", GLOB_BRACE);
        
        if (count($image) <= 0) {
            $image = glob(realpath(__DIR__ . "/../../../../../data/upload") . "/not-found.{jpg,png,gif}", GLOB_BRACE);
        }
        
        $mineType = $fileInfo->file($image[0], FILEINFO_MIME);
        
        $response = new Stream();
        $response->getHeaders()->addHeaders(array(
                "Cache-Control" => "max-age=3600, public",
                "Content-Type" => $mineType,
                "Content-Length" => filesize($image[0]),
        ));
        $response->setStream(fopen($image[0], 'r'));
        return $response;
    }
}