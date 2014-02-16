<?php

namespace Admin\Model;

use Tool\Random\RandomStringGenerater;
use Tool\File\Extension;
use Tool\File\Exception\InvalidFileFormatException;
use Tool\File\Exception\UploadFailedException;

class FileHandler
{
    
    public function addFile($fileName, $tempName)
    {
        $randomStringGenerater = new RandomStringGenerater();
        $randomStringLog = new RandomStringLog();
        $extension = new Extension(array("doc", "docx", "xls", "xlsx", "pdf", "txt", "zip", "7z"));
        $randomString = "";
        
        if ($extension->isValid($fileName)) {
            $randomString = $randomStringGenerater->getSimpleString(30);
        
            // 如果亂數已經存在於資料庫內就重新生成
            while ($randomStringLog->existsStringLog($randomString)) {
                $randomString = $randomStringGenerater->getSimpleString(30);
            }
        
            // 登記已使用過字串
            $randomStringLog->addRadomStringLog($randomString);
        
            $uploadResult = move_uploaded_file(
                    $tempName,
                    realpath(__DIR__ . "/../../../../../data/upload") . "/" . $randomString . "." . $extension->getExtension($fileName)
            );
        
            if (!$uploadResult) {
                throw new UploadFailedException();
            }
        } else {
            throw new InvalidFileFormatException();
        }
        
        return array(
            "fileName" => $randomString,
            "downloadName" => $fileName
        );
    }
    
    public function addImage($imageName, $tempName)
    {
        $randomStringGenerater = new RandomStringGenerater();
        $randomStringLog = new RandomStringLog();
        $extension = new Extension(array("jpeg", "jpg", "png"));
        $randomString = "";
    
        if ($extension->isValid($imageName)) {
            $randomString = $randomStringGenerater->getSimpleString(30);
    
            // 如果亂數已經存在於資料庫內就重新生成
            while ($randomStringLog->existsStringLog($randomString)) {
                $randomString = $randomStringGenerater->getSimpleString(30);
            }
    
            // 登記已使用過字串
            $randomStringLog->addRadomStringLog($randomString);
    
            $uploadResult = move_uploaded_file(
                $tempName,
                realpath(__DIR__ . "/../../../../../data/upload") . "/" . $randomString . "." . $extension->getExtension($imageName)
            );
    
            if (!$uploadResult) {
                throw new UploadFailedException();
            }
        } else {
            throw new InvalidFileFormatException();
        }
    
        return $randomString;
    }
    
    public function deleteFile($fileName)
    {
        $randomStringLog = new RandomStringLog();
        
        $files = glob(realpath(__DIR__ . "/../../../../../data/upload") . "/" .
                $fileName . ".{doc,docx,xls,xlsx,txt,pdf}", GLOB_BRACE);
        
        if (count($files) > 0) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        
        $randomStringLog->deleteRandomStringLog($fileName);
    }
    
    public function deleteImage($imageName)
    {
        $randomStringLog = new RandomStringLog();
        
        $oriAvatar = glob(realpath(__DIR__ . "/../../../../../data/upload") . "/" .
                $imageName . ".{jpg,png,gif}", GLOB_BRACE);
        
        if (count($oriAvatar) > 0) {
            foreach ($oriAvatar as $value) {
                unlink($value);
            }
        }
        
        $randomStringLog->deleteRandomStringLog($imageName);
    }
}