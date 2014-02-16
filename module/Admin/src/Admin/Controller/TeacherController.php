<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\TeacherModel;
use Tool\Check\FieldCheck;
use Tool\Curl\CurlTool;
use Zend\View\Model\JsonModel;

class TeacherController extends AbstractActionController
{
    public function __construct()
    {
        
    }
    
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/jquery-ui-1.10.3.custom.min.js");
        $headScript->appendFile($basePath->__invoke() . "/js/table-sort.js");
        
        $teacherModel = new TeacherModel();
        $viewModel = new ViewModel();
        
        $isSuccess = false;
        
        if ($this->getRequest()->isPost()) {
            $fieldCheck = new FieldCheck();
            $sort = $fieldCheck->checkArray($this->params()->fromPost("sort"));
            if ($teacherModel->setSort($sort)) {
                $isSuccess = true;
            }
        }
        
        $viewModel->setVariable("teacherList", $teacherModel->listTeacher());
        $viewModel->setVariable("saveSuccess", $isSuccess);
        
        return $viewModel;
    }
    
    public function booktypeAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/jquery-ui-1.10.3.custom.min.js");
        $headScript->appendFile($basePath->__invoke() . "/js/append-type-field.js");
        $headScript->appendFile($basePath->__invoke() . "/js/autofill-booktype.js");
        $teacherModel = new TeacherModel();
        $viewModel = new ViewModel();
        
        $isSuccess = false;
        
        if ($this->getRequest()->isPost()) {
            $fieldCheck = new FieldCheck();
            $types = $fieldCheck->checkArray($this->params()->fromPost("type"));
            $type_en_US = $fieldCheck->checkArray($this->params()->fromPost("type_en_US"));
            $values = $fieldCheck->checkArray($this->params()->fromPost("value"));
            if ($teacherModel->setBooktype($types, $type_en_US, $values)) {
                $isSuccess = true;
            }
        }
        
        $viewModel->setVariable("booktypeList", $teacherModel->listBookType());
        $viewModel->setVariable("saveSuccess", $isSuccess);
        
        return $viewModel;
    }
    
    public function getTypeAction()
    {
        $curlTool = new CurlTool();
        $content = array();
        $options = array();
        $types = array();
        $typeValue = "";
        
        $config = $this->getServiceLocator()->get("config");
        $content = $curlTool->getCurl($config["teacher_system_url"]);

        $content = preg_replace("/[\\S|\\s]*<select name=\".*\" id=\"ctl00_ContentPlaceHolder1_ddlFdTyp\" class=\".*\">([\\S|\\s]*)/", "\${1}", $content);
        $content = preg_replace("/<\/select>[\\S|\\s]*/", "", $content);
        $content = str_replace("\t", "", $content);
        $content = str_replace("\r", "", $content);
        $content = str_replace("</option>", "", $content);
        
        $options = split("\n", $content);
        
        foreach ($options as $i => $option) {
            $typeValue = preg_replace("/<option value=\"(\\w*)\">[\\S|\\s]*/", "\${1}", $option);
            if ($typeValue != "" && !strstr($typeValue, "全部")) {
                $types[] = array(
                    "value" => $typeValue,
                    "name" => preg_replace("/<option value=\"\\w*\">([\\S|\\s]*)/", "\${1}", $option)
                );
            }
        }
        
        return new JsonModel($types);
    }
    
    public function hireAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        $teacherModel = new TeacherModel();
        
        try {
            $editId = $fieldCheck->checkInput($this->params()->fromQuery("edit-id"));
            
            $viewModel->setVariable("editId", $editId);
            $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        } catch (\Exception $exception) {
        }
        
        if ($this->getRequest()->isPost()) {
            try {
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
                $editId = $fieldCheck->checkInput($this->params()->fromPost("edit-id"));
                $titleId = $fieldCheck->checkInput($this->params()->fromPost("title-id"));
                
                $teacherModel->setTeacherTitleById($editId, $titleId);
                
                $viewModel->setVariable("isSuccess", true);
            } catch (\Exception $exception) {
            }
        }
        
        $viewModel->setVariable("teachers", $teacherModel->listTeacher());
        $viewModel->setVariable("titles", $teacherModel->listTitle());
        return $viewModel;
    }
}