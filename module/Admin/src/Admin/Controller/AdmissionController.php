<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\AdmissionModel;
use Tool\Check\FieldCheck;

/**
 * AdmissionController
 *
 * @author
 *
 * @version
 *
 */
class AdmissionController extends AbstractActionController
{
    public function addAction()
    {
        $viewModel = new ViewModel();
        $admissionModel = new AdmissionModel();
        $fieldCheck = new FieldCheck();
        
        if ($this->getRequest()->isPost()) {
            try {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                $typeId = $fieldCheck->checkInput($this->params()->fromPost("type"));
                $content = $fieldCheck->checkEditor($this->params()->fromPost("content"));
                
                $admissionModel->addIntroduce($typeId, $content);
            } catch (\Exception $exception) {
                $this->getResponse()->setStatusCode(403);
            }
            exit();
        }
        
        $viewModel->setVariable("types", $admissionModel->listNotExistsIntroduceType());
        $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        
        // disable layout
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    public function deleteAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            
            if ($this->getRequest()->isPost()) {
                $admissionModel = new AdmissionModel();
                $admissionModel->deleteIntroduceById($id);
                
                exit();
            }
            
            $viewModel->setVariable("id", $id);
            $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        } catch (\Exception $exception) {
            $this->getResponse()->setStatausCode(403);
        }
        
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    public function editAction()
    {
        $viewModel = new ViewModel();
        $admissionModel = new AdmissionModel();
        $fieldCheck = new FieldCheck();
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            
            if ($this->getRequest()->isPost()) {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                $typeId = $fieldCheck->checkInput($this->params()->fromPost("type"));
                $content = $fieldCheck->checkEditor($this->params()->fromPost("content"));
                
                $admissionModel->setIntroduceById($id, $typeId, $content);
                exit();
            }
            
            $introduce = $admissionModel->getIntroduceById($id);
            
            $viewModel->setVariable("id", $id);
            $viewModel->setVariable("content", $introduce["content"]);
            $viewModel->setVariable("typeId", $introduce["type_id"]);
            $viewModel->setVariable("types", $admissionModel->listIntroduceType());
            $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(403);
        }
        
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    /**
     * The default action - list introduces
     */
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($basePath->__invoke() . "/ckeditor/ckeditor.js");
    
        $viewModel = new ViewModel();
        $admissionModel = new AdmissionModel();
        
        $introduces = $admissionModel->listIntroduce();
        for ($i = 0; $i < count($introduces); $i++) {
            $content = strip_tags($introduces[$i]["content"]);
            
            if (mb_strlen($content, "UTF-8") > 57) {
                $content = mb_substr($content, 0, 54, "UTF-8") . "...";
            }
            
            $introduces[$i]["content"] = $content;
        }
        
        $viewModel->setVariable("introduces", $introduces);
        return $viewModel;
    }
    
    public function typeAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($basePath->__invoke() . "/js/append-type-field.js");
        
        $viewModel = new ViewModel();
        $admissionModel = new AdmissionModel();
        $isSuccess = false;
        
        if ($this->getRequest()->isPost()) {
            $fieldCheck = new FieldCheck();
            $types = $fieldCheck->checkArray($this->params()->fromPost("type"));
            
            if ($admissionModel->setIntroduceType($types)) {
                $isSuccess = true;
            }
        }
        
        $viewModel->setVariable("types", $admissionModel->listIntroduceType());
        $viewModel->setVariable("isSuccess", $isSuccess);
        
        return $viewModel;
    }
}