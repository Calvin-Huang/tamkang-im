<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Security\AccessSecurity;
use Tool\Check\FieldCheck;
use Admin\Model\ProfileModel;
use Admin\Model\FileHandler;

class ProfileController extends AbstractActionController
{
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/holder.js");
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        
        return new ViewModel(array(
            "name" => $identity["name"],
            "username" => $identity["username"],
            "email" => $identity["email"],
            "role" => $identity["role_zhTW"],
            "avatar" => $identity["avatar"],
        ));
    }
    
    public function editAction()
    {
        
        $fieldCheck = new FieldCheck();
        $accessSecurity = new AccessSecurity();
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $errors = array();
        
        if ($this->getRequest()->isPost()) {
            $profileModel = new ProfileModel();
            
            $id = $accessSecurity->checkAccessToken($identity["t"]);
            
            $newAvatar = null;
            $name = null;
            $email = null;
            $password = null;
            
            try {
                $avatar = $fieldCheck->checkFile($this->params()->fromFiles("avatar"));
                
                $fileHandler = new FileHandler();
                $newAvatar = $fileHandler->addImage($avatar["name"], $avatar["tmp_name"]);
                $fileHandler->deleteImage($identity["avatar"]);
            } catch (\Exception $exception) {
            }
            
            try {
                $name = $fieldCheck->checkInput($this->params()->fromPost("name"));
            } catch (\Exception $exception) {
            }
            
            try {
                $fieldCheck->checkEmailFormat($this->params()->fromPost("email"));
                $email = $fieldCheck->checkInput($this->params()->fromPost("email"));
            } catch (\Exception $exception) {
            }
            
            try {
                $password = $fieldCheck->checkInput($this->params()->fromPost("password"));
            } catch (\Exception $exception) {
            }
            
            try {
                $profileModel->updateProfileById($id, $name, $email, $password, $newAvatar);
                
                $identity["name"] = $name;
                $identity["email"] = $email;
                
                if (isset($newAvatar)) {
                    $identity["avatar"] = $newAvatar;
                }
                
                $authentication->setIdentity($identity);
            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
            
            $this->redirect()->toRoute("admin/default", array("controller" => "profile"));
        } else {
            $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
            $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
            $headScript->appendFile($basePath->__invoke() . "/js/holder.js");
            $headScript->appendFile($basePath->__invoke() . "/js/image-preview.js");
        }
        
        return new ViewModel(array(
                "t" => $identity["t"],
                "name" => $identity["name"],
                "username" => $identity["username"],
                "email" => $identity["email"],
                "role" => $identity["role_zhTW"],
                "avatar" => $identity["avatar"],
                "errors" => $errors
        ));
    }
}