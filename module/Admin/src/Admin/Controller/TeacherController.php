<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\TeacherModel;
use Tool\Check\FieldCheck;

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
        $headScript->appendFile($basePath->__invoke() . "/js/append-booktype-field.js");
        $teacherModel = new TeacherModel();
        $viewModel = new ViewModel();
        
        $isSuccess = false;
        
        if ($this->getRequest()->isPost()) {
            $fieldCheck = new FieldCheck();
            $type = $fieldCheck->checkArray($this->params()->fromPost("type"));
            if ($teacherModel->setBooktype($type)) {
                $isSuccess = true;
            }
        }
        
        $viewModel->setVariable("booktypeList", $teacherModel->listBookType());
        $viewModel->setVariable("saveSuccess", $isSuccess);
        
        return $viewModel;
    }
}