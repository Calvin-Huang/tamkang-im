<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Security\AccessSecurity;
use Admin\Model\TeacherModel;
use Tool\Check\FieldCheck;
use Tool\Curl\CurlTool;
use Zend\View\Model\JsonModel;

class TeacherProfileController extends AbstractActionController
{
    public function addBookAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        $type = null;
        $informations = array();
        $profileTypes = array();
        $bookTypes = array();
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type-id"));
            
            $accessSecurity = new AccessSecurity();
            $authentication = $this->getServiceLocator()->get("authentication");
            $teacherModel = new TeacherModel();
            
            $identity = $authentication->getIdentity();
            $teacherId = $teacherModel->getTeacherIdByUserId($accessSecurity->checkAccessToken($identity["t"]));
            
            if ($this->getRequest()->isPost()) {
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
                $informations = $fieldCheck->checkArray($this->params()->fromPost("informations"));
                
                foreach ($informations as $i => $information) {
                    if (isset($information) && $information != "") {
                        $teacherModel->addBookByTeacherId($teacherId, $information, $typeId);
                    }
                }
                
                $this->redirect()->toUrl($this->getServiceLocator()->get("viewhelpermanager")->get("Url")->__invoke("admin/default", array("controller" => "teacher-profile")) . "?book-type=" . $typeId);
            }
            
            // set up exists informations and information types.
            $informations = $teacherModel->listBookByTeacherIdAndTypeId($teacherId, $typeId);
            $profileTypes = $teacherModel->listProfileType();
            $bookTypes = $teacherModel->listBookType();
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        $viewModel->setVariable("profileTypes", $profileTypes);
        $viewModel->setVariable("bookTypes", $bookTypes);
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("informations", $informations);
        $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile(
            $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath")->__invoke() . "/js/teacher-profile.js"
        );
        
        return $viewModel;
    }

    public function addProfileAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        $type = null;
        $informations = array();
        $profileTypes = array();
        $bookTypes = array();
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type-id"));
            
            $accessSecurity = new AccessSecurity();
            $authentication = $this->getServiceLocator()->get("authentication");
            $teacherModel = new TeacherModel();
            
            $identity = $authentication->getIdentity();
            $teacherId = $teacherModel->getTeacherIdByUserId($accessSecurity->checkAccessToken($identity["t"]));
            
            if ($this->getRequest()->isPost()) {
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
                $informations = $fieldCheck->checkArray($this->params()->fromPost("informations"));
                
                foreach ($informations as $i => $information) {
                    if (isset($information) && $information != "") {
                        $teacherModel->addProfileByTeacherId($teacherId, $information, $typeId);
                    }
                }
                
                $this->redirect()->toUrl($this->getServiceLocator()->get("viewhelpermanager")->get("Url")->__invoke("admin/default", array("controller" => "teacher-profile")) . "?profile-type=" . $typeId);
            }
            
            // set up exists informations and information types.
            $informations = $teacherModel->listProfileByTeacherIdAndTypeId($teacherId, $typeId);
            $profileTypes = $teacherModel->listProfileType();
            $bookTypes = $teacherModel->listBookType();
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        $viewModel->setVariable("profileTypes", $profileTypes);
        $viewModel->setVariable("bookTypes", $bookTypes);
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("informations", $informations);
        $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile(
            $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath")->__invoke() . "/js/teacher-profile.js"
        );
        
        return $viewModel;
    }
    
    public function autoGetBooksAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        try {
            // check request field if null
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type-id"));
            
            $teacherModel = new TeacherModel();
            
            // get user informations
            $accessSecurity = new AccessSecurity();
            $authentication = $this->getServiceLocator()->get("authentication");
            $identity = $authentication->getIdentity();
            
            if ($this->getRequest()->isPost()) {
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
                
                $infos = $fieldCheck->checkArray($this->params()->fromPost("infos"));
                $urls = $fieldCheck->checkArray($this->params()->fromPost("urls"));
                
                $teacherId = $teacherModel->getTeacherIdByUserId($accessSecurity->checkAccessToken($identity["t"]));
                
                $teacherModel->deleteBookByTeacherIdAndTypeId($teacherId, $typeId);
                
                foreach ($infos as $i => $info) {
                    if (isset($info) && $info != "" && isset($urls[$i]) && $urls[$i] != "") {
                        $teacherModel->addBookByTeacherId($teacherId, $info, $typeId, $urls[$i]);
                    }
                }
                
                $this->redirect()->toUrl($this->getServiceLocator()->get("viewhelpermanager")->get("Url")->__invoke("admin/default", array("controller" => "teacher-profile")) . "?book-type=" . $typeId);
            }
            
            $config = $this->getServiceLocator()->get("config");
            
            $books = $teacherModel->getBooks($config, $teacherModel->getBooktypeValueById($typeId), $identity["name"]);
            
            $viewModel->setVariable("informations", $books);
            $viewModel->setVariable("profileTypes", $teacherModel->listProfileType());
            $viewModel->setVariable("bookTypes", $teacherModel->listBookType());
            $viewModel->setVariable("typeId", $typeId);
            $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        return $viewModel;
    }
    
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
    
    public function deleteBookAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        $id = null;
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            
            if ($this->getRequest()->isPost()) {
                
                // check csrf token, if not throw exception
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
                
                $teacherModel = new TeacherModel();
                $teacherModel->deleteBookById($id);
                
                exit();
            }
        } catch (\Exception $exception) {
            
            // if have not receive specific id, send status code 404 page not found.
            $this->getResponse()->setStatusCode(404);
        }
        
        // because this is just a alert message so disable the layout
        $viewModel->setTerminal(true);
        $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        $viewModel->setVariable("id", $id);
        
        return $viewModel;
    }
    
    public function deleteProfileAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        $id = null;
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
        
            if ($this->getRequest()->isPost()) {
        
                // check csrf token, if not throw exception
                $fieldCheck->checkToken($this->params()->fromPost("csrf-token"));
        
                $teacherModel = new TeacherModel();
                $teacherModel->deleteProfilebyId($id);
        
                exit();
            }
        } catch (\Exception $exception) {
        
            // if have not receive specific id, send status code 404 page not found.
            $this->getResponse()->setStatusCode(404);
        }
        
        // because this is just a alert message so disable the layout
        $viewModel->setTerminal(true);
        $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        $viewModel->setVariable("id", $id);
        
        return $viewModel;
    }
    
    public function editBookAction()
    {
        $viewModel = new ViewModel();
        $teacherModel = new TeacherModel();
        $fieldCheck = new FieldCheck();
        $accessSecurity = new AccessSecurity();
        
        $profileTypes = $teacherModel->listProfileType();
        
        // book 是教師教學資料或是論文等等
        $bookTypes = $teacherModel->listBookType();
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $userId = $accessSecurity->checkAccessToken($identity["t"]);
        $teacherId = $teacherModel->getTeacherIdByUserId($userId);
        
        $typeId = null;
        $id = null;
        $informations = array();
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type_id"));
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            
            if ($this->getRequest()->isPost()) {
                $title = $fieldCheck->checkInput($this->params()->fromPost("title"));
                
                $teacherModel->setBookById($id, $title);
                $this->redirect()->toUrl($this->getServiceLocator()->get("viewhelpermanager")->get("Url")->__invoke("admin/default", array("controller" => "teacher-profile")) . "?book-type=" . $typeId);
            }
            
            $informations = $teacherModel->listBookByTeacherIdAndTypeId($teacherId, $typeId);
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode("404");
        }
        
        $viewModel->setVariable("profileTypes", $profileTypes);
        $viewModel->setVariable("bookTypes", $bookTypes);
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("id", $id);
        $viewModel->setVariable("informations", $informations);
        $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
        
        // add javascript to display edit field at view top
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile(
                $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath")->__invoke() . "/js/teacher-profile.js"
        );
        
        return $viewModel;
    }
    
    public function editProfileAction()
    {
        $viewModel = new ViewModel();
        $teacherModel = new TeacherModel();
        $fieldCheck = new FieldCheck();
        $accessSecurity = new AccessSecurity();
    
        $profileTypes = $teacherModel->listProfileType();
    
        // book 是教師教學資料或是論文等等
        $bookTypes = $teacherModel->listBookType();
    
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $userId = $accessSecurity->checkAccessToken($identity["t"]);
        $teacherId = $teacherModel->getTeacherIdByUserId($userId);
    
        $typeId = null;
        $id = null;
        $informations = array();
    
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type_id"));
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
    
            if ($this->getRequest()->isPost()) {
                $title = $fieldCheck->checkInput($this->params()->fromPost("title"));
    
                $teacherModel->setProfileById($id, $title);
                $this->redirect()->toUrl($this->getServiceLocator()->get("viewhelpermanager")->get("Url")->__invoke("admin/default", array("controller" => "teacher-profile")) . "?profile-type=" . $typeId);
            }
    
            $informations = $teacherModel->listProfileByTeacherIdAndTypeId($teacherId, $typeId);
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode("404");
        }
    
        $viewModel->setVariable("profileTypes", $profileTypes);
        $viewModel->setVariable("bookTypes", $bookTypes);
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("id", $id);
        $viewModel->setVariable("informations", $informations);
        $viewModel->setVariable("csrfToken", $fieldCheck->createToken("tamkang-im"));
    
        // add javascript to display edit field at view top
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile(
                $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath")->__invoke() . "/js/teacher-profile.js"
        );
    
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
    
    public function getBooksAction()
    {
        $curlTool = new CurlTool();
        $fieldCheck = new FieldCheck();
        $config = $this->getServiceLocator()->get("config");
        $data = array();
        
        try {
            $typeValue = $fieldCheck->checkInput($this->params()->fromQuery("type_value"));
            $teacherName = $fieldCheck->checkInput($this->params()->fromQuery("teacher_name"));
            
            $contents[] = $curlTool->getCurl($config["teacher_system_url"] . "?s=" . $typeValue . "&kwd=" . urlencode($teacherName));
            
            $pageBlock = explode("<div class=\"x_pager_style1\">", $contents[0]);
            
            // 有頁數區塊才有資料
            if (count($pageBlock) > 1) {
                $pageBlock = explode("</div>", $pageBlock[1]);
                $pageBlock = $pageBlock[0];
                
                // 取得所有資料數量，一頁30筆，算出所有頁數
                $totalPages = ceil(((int)(preg_replace("/[\\s\\S]*共有 (\\d+) 筆查詢結果[\\s\\S]*/", "\${1}", $pageBlock))) / 30);
                
                // 取得所有頁面內容
                if ($totalPages > 1) {
                    for ($i = 1; $i < $totalPages; $i++) {
                        $contents[] = $curlTool->getCurl($config["teacher_system_url"] . "/StaffSummary.aspx?s=" . $typeValue . "&kwd=" . urlencode($teacherName) . "&pg=" . ($i + 1));
                    }
                }
                
                foreach ($contents as $i => $content) {
                    
                    // 以id ctl00_ContentPlaceHolder1_divCtn切割掉前面不要的區塊
                    $infoBlock = explode("ctl00_ContentPlaceHolder1_divCtn", $content);
                    $infoBlock = explode("<tbody>", $infoBlock[1]);
                    $infoBlock = explode("</tbody>", $infoBlock[1]);
                    $infoBlock = $infoBlock[0];
                    
                    $infoBlock = strip_tags($infoBlock, "<tr><a>");
                    $infoBlock = str_replace("\n", "", $infoBlock);
                    $infoBlock = str_replace("\r", "", $infoBlock);
                    
                    $dataRows = split("</tr>", $infoBlock);
                    $temp = "";
                    
                    foreach ($dataRows as $i => $dataRow) {
                        $dataRow = explode("發佈", $dataRow);
                        
                        if (isset($dataRow[1]) && $dataRow[1] != "") {
                            $temp = explode("href=\"", $dataRow[1]);
                            $temp = explode("\"", $temp[1]);
                            
                            $data[] = array(
                                "url" => $config["teacher_system_url"] . "/" . $temp[0],
                                "info" => strip_tags($dataRow[1]) 
                            );
                        }
                    }
                }
            }
            
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        return new JsonModel($data);
    }
    
    public function indexAction()
    {
        $fieldCheck = new FieldCheck();
        $viewModel = new ViewModel();
        $teacherModel = new TeacherModel();
        $accessSecurity = new AccessSecurity();
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $userId = $accessSecurity->checkAccessToken($identity["t"]);
        $teacherId = $teacherModel->getTeacherIdByUserId($userId);
        
        $profileTypes = $teacherModel->listProfileType();
        
        // book 是教師教學資料或是論文等等
        $bookTypes = $teacherModel->listBookType();
        
        $typeId = null;
        $actionType = "profile";
        
        $informations = array();
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("profile-type"));
        } catch (\Exception $exception) {
            $typeId = $profileTypes[0]["id"];
        }
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("book-type"));
            
            $actionType = "book";
        } catch (\Exception $exception) {
            
        }
        
        if ($actionType == "book") {
            $informations = $teacherModel->listBookByTeacherIdAndTypeId($teacherId, $typeId);
        } else {
            $informations = $teacherModel->listProfileByTeacherIdAndTypeId($teacherId, $typeId);
        }
        
        $viewModel->setVariable("profileTypes", $profileTypes);
        $viewModel->setVariable("bookTypes", $bookTypes);
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("informations", $informations);
        $viewModel->setVariable("actionType", $actionType);
        
        return $viewModel;
    }
}