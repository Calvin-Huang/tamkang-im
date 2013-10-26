<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Security\AccessSecurity;
use Admin\Model\TeacherModel;
use Tool\Check\FieldCheck;

class TeacherProfileController extends AbstractActionController
{
    public function bookAction()
    {
        $accessSecurity = new AccessSecurity();
        $authentication = $this->getServiceLocator()->get("authentication");
        $teacherModel = new TeacherModel();
        
        $identity = $authentication->getIdentity();
        $teacherId = $teacherModel->getTeacherIdByUserId($accessSecurity->checkAccessToken($identity["t"]));
        
        $bookTypeList = $teacherModel->listBookType();
        $bookList = array();
        
        foreach ($bookTypeList as $key => $value) {
            $bookList[$value["id"]] = $teacherModel->listBookByTeacherIdAndTypeId($teacherId, $value["id"]);
        }

        return new ViewModel(array(
            "bookList" => $bookList,
            "bookTypeList" => $bookTypeList,
        ));
    }
    
    public function editBookAction()
    {
        $this->getServiceLocator()->get("navigation/admin")->findOneBy("id", "teacher-book")->setActive();
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/append-book-field.js");
        
        $accessSecurity = new AccessSecurity();
        $authentication = $this->getServiceLocator()->get("authentication");
        $teacherModel = new TeacherModel();
        $fieldCheck = new FieldCheck();
        
        $identity = $authentication->getIdentity();
        
        try {
            $teacherId = $teacherModel->getTeacherIdByUserId($accessSecurity->checkAccessToken($identity["t"]));
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type_id"));
            
            if ($this->getRequest()->isPost()) {
                // $fieldCheck->checkToken($this->params()->fromPost("t"));
                $books = $fieldCheck->checkArray($this->params()->fromPost("book"));
                
                // 先把舊的專書刪除
                $teacherModel->deleteBookByTeacherIdAndTypeId($teacherId, $typeId);
                foreach ($books as $i => $title) {
                    if (isset($title) && $title != "") {
                        $teacherModel->addBookByTeacherId($teacherId, $title, $typeId);
                    }
                }
                
                $this->redirect()->toRoute("admin/default", array("controller" => "teacher-profile", "action" => "book"));
            }
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        $bookList = array();
        $bookList = $teacherModel->listBookByTeacherIdAndTypeId($teacherId, $typeId);
        
        return new ViewModel(array(
            "bookList" => $bookList,
            "id" => $typeId,
            "token" => $fieldCheck->createToken("tamkang-im"),
            "typeName" => $teacherModel->getBooktypeNameById($typeId),
        ));
    }
    
    public function editLabsiteAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        
        return $viewModel;
    }
    
    public function editInfoAction()
    {
        $this->getServiceLocator()->get("navigation/admin")->findOneBy("id", "teacher-profile")->setActive();
        
        $viewModel = new ViewModel();
        $teacherModel = new TeacherModel();
        $accessSecurity = new AccessSecurity();
        $fieldCheck = new FieldCheck();
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $userId = $accessSecurity->checkAccessToken($identity["t"]);
        $teacherId = $teacherModel->getTeacherIdByUserId($userId);
        
        if ($this->getRequest()->isPost()) {
            $fieldCheck->checkToken($this->params()->fromPost("t"));
            
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
                
                $this->redirect()->toRoute("admin/default", array("controller" => "teacher-profile", "action" => "index"));
            } catch (\Exception $exception) {
                $this->getResponse()->setStatusCode(404);
            }
        }
        
        $teacherInfo = $teacherModel->getTeacherByUserId($userId);
        
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/append-othertitle-field.js");
        
        $viewModel->setVariable("titles", $teacherModel->listTitle());
        $viewModel->setVariable("titleId", $teacherModel->getTeacherTitleIdByUserId($userId));
        $viewModel->setVariable("othertitles", $teacherModel->listOthertitleByTeacherId($teacherId));
        $viewModel->setVariable("teachClass", (isset($teacherInfo["teach_class"])) ? $teacherInfo["teach_class"] : "");
        $viewModel->setVariable("labLocation", (isset($teacherInfo["lab_location"])) ? $teacherInfo["lab_location"] : "");
        $viewModel->setVariable("labsiteText", (isset($teacherInfo["labsite_text"])) ? $teacherInfo["labsite_text"] : "");
        $viewModel->setVariable("labsiteUrl", (isset($teacherInfo["labsite_url"])) ? $teacherInfo["labsite_url"] : "http://");
        $viewModel->setVariable("personalsiteText", (isset($teacherInfo["personalsite_text"])) ? $teacherInfo["personalsite_text"] : "");
        $viewModel->setVariable("personalsiteUrl", (isset($teacherInfo["personalsite_url"])) ? $teacherInfo["personalsite_url"] : "http://");
        $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        
        return $viewModel;
    }
    
    public function editProfileAction()
    {
        $this->getServiceLocator()->get("navigation/admin")->findOneBy("id", "teacher-profile")->setActive();
        
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/append-profile-field.js");
        
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        $teacherModel = null;
        $typeId = null;
        $profiles = array();
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type_id"));
            
            $accessSecurity = new AccessSecurity();
            $teacherModel = new TeacherModel();
            $authentication = $this->getServiceLocator()->get("authentication");
            $identity = $authentication->getIdentity();
            
            $userId = $accessSecurity->checkAccessToken($identity["t"]);
            $teacherId = $teacherModel->getTeacherIdByUserId($userId);
            
            if ($this->getRequest()->isPost()) {
                $profiles = $fieldCheck->checkArray($this->params()->fromPost("profile"));
                
                // 先刪除所有的教師個人資料
                $teacherModel->deleteProfileByTeacherIdAndTypeId($teacherId, $typeId);
                foreach ($profiles as $i => $profile) {
                    if (isset($profile) && $profile != "") {
                        $teacherModel->addProfileByTeacherId($teacherId, $profile, $typeId);
                    }
                }
                
                $this->redirect()->toRoute("admin/default", array("controller" => "teacher-profile", "action" => "index"));
            }
            
            $profiles = $teacherModel->listProfileByTeacherIdAndTypeId($teacherId, $typeId);
            
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("profiles", $profiles);
        $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        $viewModel->setVariable("typeName", $teacherModel->getProfileNameById($typeId));
        
        return $viewModel;
    }
    
    public function indexAction()
    {
        $viewModel = new ViewModel();
        $teacherModel = new TeacherModel();
        $accessSecurity = new AccessSecurity();
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $userId = $accessSecurity->checkAccessToken($identity["t"]);
        $teacherId = $teacherModel->getTeacherIdByUserId($userId);
        
        $teacherInfo = $teacherModel->getTeacherByUserId($userId);
        $profileTypes = $teacherModel->listProfileType();
        $profiles = array();
        
        foreach ($profileTypes as $i => $profileType) {
            $profiles[$profileType["id"]] = $teacherModel->listProfileByTeacherIdAndTypeId($teacherId, $profileType["id"]);
        }
        
        $viewModel->setVariable("title", $teacherInfo["title_name"]);
        $viewModel->setVariable("othertitles", $teacherModel->listOthertitleByTeacherId($teacherId));
        $viewModel->setVariable("teachClass", (isset($teacherInfo["teach_class"]) && $teacherInfo["teach_class"] != "") ? $teacherInfo["teach_class"] : "無");
        $viewModel->setVariable("labLocation", (isset($teacherInfo["lab_location"]) && $teacherInfo["lab_location"] != "") ? $teacherInfo["lab_location"] : "無");
        $viewModel->setVariable("labsiteText", (isset($teacherInfo["labsite_text"]) && $teacherInfo["labsite_text"] != "") ? $teacherInfo["labsite_text"] : "無");
        $viewModel->setVariable("labsiteUrl", (isset($teacherInfo["labsite_url"]) && $teacherInfo["labsite_url"] != "") ? $teacherInfo["labsite_url"] : "");
        $viewModel->setVariable("personalsiteText", (isset($teacherInfo["personalsite_text"]) && $teacherInfo["personalsite_text"] != "") ? $teacherInfo["personalsite_text"] : "無");
        $viewModel->setVariable("personalsiteUrl", (isset($teacherInfo["personalsite_url"]) && $teacherInfo["personalsite_url"] != "") ? $teacherInfo["personalsite_url"] : "");
        
        $viewModel->setVariable("profileTypes", $profileTypes);
        $viewModel->setVariable("profiles", $profiles);
        
        return $viewModel;
    }
}