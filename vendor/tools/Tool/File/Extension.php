<?php

namespace Tool\File;

class Extension
{
    private $extension = null;
    private $error = null;
    public function __construct($extension = array())
    {
        $this->extension = $extension;
        $this->error = array();
    }
    
    public function __destruct()
    {
        
    }
    
    public function isValid($fileName)
    {
        $nameFrag = explode(".", $fileName);
        $extension = $nameFrag[count($nameFrag) - 1];
            
        foreach ($this->extension as $value) {
            if (strtolower($extension) == $value) {
                return true;
            }
        }
        
        $this->error[] = "檔案非允許格式";
        
        return false;
    }
    
    public function getMessages()
    {
        return $this->error;
    }
    
    public function getExtension($fileName)
    {
        $nameFrag = explode(".", $fileName);
        $extension = $nameFrag[count($nameFrag) - 1];
        return strtolower($extension);
    }
}
?>