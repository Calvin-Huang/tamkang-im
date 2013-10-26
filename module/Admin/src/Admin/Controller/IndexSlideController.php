<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\IndexSlideModel;
use Tool\Check\FieldCheck;
use Tool\File\Exception\InvalidFileFormatException;
use Tool\File\Exception\UploadFailedException;
use Admin\Model\FileHandler;

class IndexSlideController extends AbstractActionController
{
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/image-preview.js");
        
        $fieldCheck = new FieldCheck();
        $viewModel = new ViewModel();
        
        if ($this->getRequest()->isPost()) {
            try {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                $fileHandler = new FileHandler();
                $indexSlideModel = new IndexSlideModel();
                
                try {
                    $link = $fieldCheck->checkInput($this->params()->fromPost("link"));
                } catch (\Exception $exception) {
                }
                
                $linkText = "";
                try {
                    $linkText = $fieldCheck->checkInput($this->params()->fromPost("link-text"));
                } catch (\Exception $exception) {
                }
                
                $slideImage = $fieldCheck->checkFile($this->params()->fromFiles("slide-image"));
                $slideImageName = $fileHandler->addImage($slideImage["name"], $slideImage["tmp_name"]);

                $indexSlideModel->addIndexSlide($slideImageName);
            } catch (UploadFailedException $exception) {
                echo $exception->getMessage();
            } catch (InvalidFileFormatException $exception) {
                echo $exception->getMessage();
            } catch (\Exception $exception) {
                $this->getResponse()->setStatusCode(403);
            }
            
            exit();
        } else {
            $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        }
        
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
    
    public function deleteAction()
    {
        $fieldCheck = new FieldCheck();
        $id = null;
    
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            $userModel = new IndexSlideModel();
    
            // 進行完動作之後不render view
            if ($this->getRequest()->isPost()) {
                $indexSlideModel = new IndexSlideModel();
                $fileHandler = new FileHandler();
                
                // 刪除圖片
                $slideImage = $indexSlideModel->getSlideImageById($id);
                if ($slideImage != "") {
                    $fileHandler->deleteImage($slideImage);
                }
                $indexSlideModel->deleteIndexSlideById($id);
                exit();
            }
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(403);
        }
    
        $viewModel = new ViewModel(array(
                "token" => $fieldCheck->createToken("tamkang-im"),
                "id" => $id,
        ));
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
    
    public function editAction()
    {
        $fieldCheck = new FieldCheck();
        $viewModel = new ViewModel();
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            $indexSlideModel = new IndexSlideModel();
            
            if ($this->getRequest()->isPost()) {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                $slideImageName = "";
                try {
                    $slideImage = $fieldCheck->checkFile($this->params()->fromFiles("slide-image"));
                    
                    // 刪除圖片
                    $fileHandler = new FileHandler();
                    $fileHandler->deleteImage($indexSlideModel->getSlideImageById($id));
                    
                    $slideImageName = $fileHandler->addImage($slideImage["name"], $slideImage["tmp_name"]);
                } catch (\Exception $exception) {
                }
                
                $indexSlideModel->setIndexSlideById($id, $slideImageName);
                exit();
            } else {
                $indexSlide = $indexSlideModel->getIndexSlideById($id);
                
                $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
                $viewModel->setVariable("image", $indexSlide["image"]);
                $viewModel->setVariable("id", $id);
            }
        } catch (\Exception $exception) {
            $this->getRequest()->setStatusCode(403);
        }
        
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
    
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/jquery-ui-1.10.3.custom.min.js");
        $headScript->appendFile($basePath->__invoke() . "/js/table-sort.js");
        
        $indexSlideModel = new IndexSlideModel();
        $fieldCheck = new FieldCheck();
        $isSuccess = null;
        
        try {
            $sort = $fieldCheck->checkArray($this->params()->fromPost("sort"));
            $indexSlideModel->setIndexSlideSort($sort);
            $isSuccess = true;
        } catch (\PDOException $exception) {
            $isSuccess = false;
        } catch (\Exception $exception) {
        }
        
        $indexSlideList = $indexSlideModel->listIndexSlide();
        
        return new ViewModel(array(
            "indexSlideList" => $indexSlideList,
            "token" => $fieldCheck->createToken("tamkang-im"),
            "isSuccess" => $isSuccess
        ));
    }
}
?>