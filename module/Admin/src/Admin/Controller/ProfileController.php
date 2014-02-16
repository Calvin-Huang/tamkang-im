<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Security\AccessSecurity;
use Tool\Check\FieldCheck;
use Admin\Model\ProfileModel;
use Admin\Model\FileHandler;
use Admin\Model\TeacherModel;

class ProfileController extends AbstractActionController
{
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/holder.js");
        
        $viewModel = new ViewModel();
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        
        if ($identity["role"] == "teacher") {
            $teacherModel = new TeacherModel();
            $accessSecurity = new AccessSecurity();
            $userId = $accessSecurity->checkAccessToken($identity["t"]);
            
            $teacherId = $teacherModel->getTeacherIdByUserId($userId);
            
            $teacherInfo = $teacherModel->getTeacherByUserId($userId);
            
            $viewModel->setVariable("title", $teacherInfo["title_name"]);
            $viewModel->setVariable("othertitles", $teacherModel->listOthertitleByTeacherId($teacherId));
            $viewModel->setVariable("teachClass", (isset($teacherInfo["teach_class"]) && $teacherInfo["teach_class"] != "") ? $teacherInfo["teach_class"] : "無");
            $viewModel->setVariable("labLocation", (isset($teacherInfo["lab_location"]) && $teacherInfo["lab_location"] != "") ? $teacherInfo["lab_location"] : "無");
            $viewModel->setVariable("labsiteText", (isset($teacherInfo["labsite_text"]) && $teacherInfo["labsite_text"] != "") ? $teacherInfo["labsite_text"] : "無");
            $viewModel->setVariable("labsiteUrl", (isset($teacherInfo["labsite_url"]) && $teacherInfo["labsite_url"] != "") ? $teacherInfo["labsite_url"] : "");
        }
        
        $viewModel->setVariable("name", $identity["name"]);
        $viewModel->setVariable("username", $identity["username"]);
        $viewModel->setVariable("email", $identity["email"]);
        $viewModel->setVariable("role", $identity["role"]);
        $viewModel->setVariable("role_zhTW", $identity["role_zhTW"]);
        $viewModel->setVariable("avatar", $identity["avatar"]);
        
        return $viewModel;
    }
    
    public function editAction()
    {
        
        $fieldCheck = new FieldCheck();
        $accessSecurity = new AccessSecurity();
        
        $viewModel = new ViewModel();
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $errors = array();
        
        if ($this->getRequest()->isPost()) {
            try {
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
            } catch (\Exception $exception) {
                exit();
            }
            
            $profileModel = new ProfileModel();
            
            $id = $accessSecurity->checkAccessToken($identity["t"]);
            
            $newAvatar = null;
            $name = null;
            $email = null;
            $password = null;
            
            try {
                $fieldCheck->checkEmailFormat($this->params()->fromPost("email"));
                $email = $fieldCheck->checkInput($this->params()->fromPost("email"));
            } catch (\Exception $exception) {
            }
            
            try {
                $profileModel->updateProfileById($id, $name, $email, $password, $newAvatar);

                $identity["email"] = $email;
                
                $authentication->setIdentity($identity);
            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
            
            if ($identity["role"] == "teacher") {
                $teacherModel = new TeacherModel();
                $accessSecurity = new AccessSecurity();
                $userId = $accessSecurity->checkAccessToken($identity["t"]);
                
                $teacherId = $teacherModel->getTeacherIdByUserId($userId);
                
                $titleId = null;
                $othertitle = array();
                $teachClass = "";
                $labLocation = "";
                $labsiteText = "";
                $labsiteUrl = "";
                $personalsiteText = "";
                $personalsiteUrl = "";
                
                try {
                    $titleId = $fieldCheck->checkInput($this->params()->fromPost("title"));
                } catch (\Exception $exception) {
                }
                
                try {
                    $othertitle = $fieldCheck->checkArray($this->params()->fromPost("othertitle"));
                    $teacherModel->setOthertitleByTeacherId($teacherId, $othertitle);
                } catch (\Exception $exception) {
                }
                
                try {
                    $teachClass = $fieldCheck->checkInput($this->params()->fromPost("teach-class"));
                } catch (\Exception $exception) {
                }
                
                try {
                    $labLocation = $fieldCheck->checkInput($this->params()->fromPost("lab-location"));
                } catch (\Exception $exception) {
                }
                
                try {
                    $labsiteText = $fieldCheck->checkInput($this->params()->fromPost("labsite-text"));
                    $labsiteUrl = $fieldCheck->checkUrlFormat($this->params()->fromPost("labsite-url"));
                
                } catch (\Exception $exception) {
                }
                
                try {
                    $personalsiteText = $fieldCheck->checkInput($this->params()->fromPost("personalsite-text"));
                    $personalsiteUrl = $fieldCheck->checkUrlFormat($this->params()->fromPost("personalsite-url"));
                } catch (\Exception $exception) {
                }
                
                try {
                    $teacherModel->setTeacherByUserId($userId, $titleId, $teachClass, $labLocation, $labsiteText, $labsiteUrl, $personalsiteText, $personalsiteUrl);
                } catch (\Exception $exception) {
                    $errors[] = $exception->getMessage();
                }
            }
            
            $this->redirect()->toRoute("admin/default", array("controller" => "profile"));
        } else {
            $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
            $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
            $headScript->appendFile($basePath->__invoke() . "/js/holder.js");
            $headScript->appendFile($basePath->__invoke() . "/js/image-preview.js");
        }
        
        if ($identity["role"] == "teacher") {
            $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($this->getServiceLocator()->get("viewhelpermanager")->get("BasePath")->__invoke() . "/js/append-othertitle-field.js");
            
            $teacherModel = new TeacherModel();
            $accessSecurity = new AccessSecurity();
            $userId = $accessSecurity->checkAccessToken($identity["t"]);
            
            $teacherId = $teacherModel->getTeacherIdByUserId($userId);
            
            $teacherInfo = $teacherModel->getTeacherByUserId($userId);
            
            $viewModel->setVariable("title", $teacherInfo["title_name"]);
            $viewModel->setVariable("othertitles", $teacherModel->listOthertitleByTeacherId($teacherId));
            $viewModel->setVariable("teachClass", (isset($teacherInfo["teach_class"])) ? $teacherInfo["teach_class"] : "");
            $viewModel->setVariable("labLocation", (isset($teacherInfo["lab_location"])) ? $teacherInfo["lab_location"] : "");
            $viewModel->setVariable("labsiteText", (isset($teacherInfo["labsite_text"])) ? $teacherInfo["labsite_text"] : "");
            $viewModel->setVariable("labsiteUrl", (isset($teacherInfo["labsite_url"])) ? $teacherInfo["labsite_url"] : "http://");
            $viewModel->setVariable("personalsiteText", (isset($teacherInfo["personalsite_text"])) ? $teacherInfo["personalsite_text"] : "");
            $viewModel->setVariable("personalsiteUrl", (isset($teacherInfo["personalsite_url"])) ? $teacherInfo["personalsite_url"] : "http://");
        }
        
        $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        $viewModel->setVariable("name", $identity["name"]);
        $viewModel->setVariable("username", $identity["username"]);
        $viewModel->setVariable("email", $identity["email"]);
        $viewModel->setVariable("role", $identity["role"]);
        $viewModel->setVariable("role_zhTW", $identity["role_zhTW"]);
        $viewModel->setVariable("avatar", $identity["avatar"]);
        $viewModel->setVariable("errors", $errors);
        
        return $viewModel;
    }
    
    public function editAvatarAction()
    {
        $fieldCheck = new FieldCheck();
        $accessSecurity = new AccessSecurity();
        
        // get user authencation
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $errors = array();
        
        if ($this->getRequest()->isPost()) {
            try {
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
            } catch (\Exception $exception) {
                exit();
            }
            
            $profileModel = new ProfileModel();
            
            $id = $accessSecurity->checkAccessToken($identity["t"]);
            
            $newAvatar = null;
            $name = null;
            $email = null;
            $password = null;
            
            try {
                
                // get new avatar
                $avatar = $fieldCheck->checkFile($this->params()->fromFiles("avatar"));
            
                $fileHandler = new FileHandler();
                
                // add new avatar file
                $newAvatar = $fileHandler->addImage($avatar["name"], $avatar["tmp_name"]);
                
                // delete exists avatar file
                $fileHandler->deleteImage($identity["avatar"]);
            } catch (\Exception $exception) {
            }
            
            try {
                $profileModel->updateProfileById($id, $name, $email, $password, $newAvatar);
            
                if (isset($newAvatar)) {
                    $identity["avatar"] = $newAvatar;
                }
            
                // reset user identity setting
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
                "errors" => $errors,
                "csrfToken" => $fieldCheck->createToken("tamkang-im")
        ));
    }
    
    public function editPasswordAction()
    {
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($this->getServiceLocator()->get("viewhelpermanager")->get("BasePath")->__invoke() . "/js/jquery.validate.min.js");
        $fieldCheck = new FieldCheck();
        $viewModel = new ViewModel(array(
                "csrfToken" => $fieldCheck->createToken("tamkang-im")
        ));
        
        if ($this->getRequest()->isPost()) {
            try {
                $accessSecurity = new AccessSecurity();
                $authentication = $this->getServiceLocator()->get("authentication");
                $identity = $authentication->getIdentity();
            
                $password = $fieldCheck->checkInput($this->params()->fromPost("old"));
                $id = $accessSecurity->checkAccessToken($identity["t"]);
            
                $profileModel = new ProfileModel();
            
                $newPassword = $fieldCheck->checkInput($this->params()->fromPost("new"));
                $checkPassword = $fieldCheck->checkInput($this->params()->fromPost("check"));
            
                if ($profileModel->getPasswordById($id) != sha1(md5($password))) {
                    throw new \Exception("密碼確認錯誤");
                }
            
                if ($newPassword != $checkPassword) {
                    throw new \Exception("兩次輸入密碼不同");
                }
            
                $profileModel->updateProfileById($id, null, null, $newPassword, null);
            
                $this->redirect()->toRoute("admin/default", array("controller" => "profile"));
            } catch (\Exception $exception) {
                $viewModel->setVariable("errorMessage", $exception->getMessage());
            }
        }
        
        return $viewModel;
    }
}