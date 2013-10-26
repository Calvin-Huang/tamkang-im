<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Tool\Check\FieldCheck;
use Admin\Model\UserModel;
use Tool\Page\Paginator;
use Admin\Model\TeacherModel;

class UserController extends AbstractActionController
{
    public function __construct()
    {
        $session = new Container();
    }
    
    public function addAction()
    {
        $fieldCheck = new FieldCheck();
        $userModel = new UserModel();
        $viewModel = new ViewModel();
        
        if (!$this->getRequest()->isPost()) {
            $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
            $viewModel->setVariable("roleList", $userModel->listRole());
        } else {
            try {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                $username = $fieldCheck->checkInput($this->params()->fromPost("username"));
                $name = $fieldCheck->checkInput($this->params()->fromPost("name"));
                $email = $fieldCheck->checkInput($this->params()->fromPost("email"));
                $fieldCheck->checkEmailFormat($email);
                $password = $fieldCheck->checkInput($this->params()->fromPost("password"));
                $roleId = $fieldCheck->checkInput($this->params()->fromPost("role"));
                
                if (!$userModel->existsUsername($username)) {
                    $id = $userModel->addUser($username, $name, $email, $password, $roleId);
                    
                    // 如果新增的用戶是教師，則還要在另外加入教師紀錄
                    if ($roleId == 3) {
                        $teacherModel = new TeacherModel();
                        $teacherModel->addTeacherByUserId($id);
                    }
                }
                exit();
            } catch (\Exception $exception) {
                $this->getResponse()->setStatusCode(404);
            }
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
            $userModel = new UserModel();
            
            // 進行完動作之後不render view
            if ($this->getRequest()->isPost()) {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                $user = $userModel->getUserById($id);
                
                // 如果是教師用戶的話要在另外刪除教師資料
                if ($user["role_id"] == 3) {
                    $teacher = new TeacherModel();
                    $teacher->deleteTeacherByUserId($id);
                }
                
                $userModel->deleteUserById($id);
                exit();
            }
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
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
        $userModel = null;
        $id = null;
        $user = null;
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            $userModel = new UserModel();
            
            // 進行完動作之後不render view
            if ($this->getRequest()->isPost()) {
                
                // 進行修改動作
                $username = null;
                $name = null;
                $email = null;
                $password = null;
                $roleId = null;
                
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                try {
                    $name = $fieldCheck->checkInput($this->params()->fromPost("name"));
                } catch (\Exception $exception) {
                }
                
                try {
                    $email = $fieldCheck->checkInput($this->params()->fromPost("email"));
                    $fieldCheck->checkEmailFormat($email);
                } catch (\Exception $exception) {
                }
                
                try {
                    $password = $fieldCheck->checkInput($this->params()->fromPost("password"));
                } catch (\Exception $exception) {
                }
                
                try {
                    $roleId = $fieldCheck->checkInput($this->params()->fromPost("role"));
                    $teacherModel = new TeacherModel();
                    
                    // 如果是教師用戶的話還要新增教師紀錄
                    if ($roleId == 3) {
                        $teacherId = $teacherModel->getTeacherIdByUserId($id);
                        if (!isset($teacherId) && $teacherId == "") {
                            $teacherModel->addTeacherByUserId($id);
                        }
                    } else {
                        $teacherModel->deleteTeacherByUserId($id);
                    }
                } catch (\Exception $exception) {
                }
                
                $userModel->setUserById($id, $name, $email, $password, $roleId);
                exit();
            } else {
                $user = $userModel->getUserById($id);
            }
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        $viewModel = new ViewModel(array(
            "user" => $user,
            "token" => $fieldCheck->createToken("tamkang-im"),
            "roleList" => $userModel->listRole(),
            "id" => $id,
        ));
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
    
    public function indexAction()
    {
        // 加入javascript
        
        $fieldCheck = new FieldCheck();
        $currentPage = null;
        $onePageDisplay = 20;
        $term = null;
        $role = null;
        
        $currentPage = $fieldCheck->checkPage($this->params()->fromQuery("page"));
        
        try {
            $term = $fieldCheck->checkInput($this->params()->fromQuery("term"));
        } catch (\Exception $exception) {
        }
        
        try {
            $role = $fieldCheck->checkInput($this->params()->fromQuery("role"));
        } catch (\Exception $exception) {
        }

        $userModel = new UserModel($onePageDisplay);
        $userList = $userModel->listUser($currentPage, $term, $role);
        
        return new ViewModel(array(
            "paginator" => Paginator::factory($currentPage, $onePageDisplay, $userList[0]),
            "userList" => $userList[1],
            "token" => $fieldCheck->createToken("tamkang-im"),
            "roleList" => $userModel->listRole(),
            "role" => $role,
            "term" => $term,
        ));
    }
}
