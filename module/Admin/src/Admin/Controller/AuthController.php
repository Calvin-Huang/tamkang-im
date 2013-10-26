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
use Tool\Check\Exception\FieldEmptyException;
use Tool\Check\Exception\TokenNotCorrectException;
use Tool\Check\Exception\TokenEmptyException;

class AuthController extends AbstractActionController
{
    public function __construct()
    {
        $session = new Container();
    }
    
    public function indexAction()
    {
        // 取得view helper
        // $navigationHelper = $this->getServiceLocator()->get("viewhelpermanager")->get("NavigationHelper");
        // $this->layout()->test = "hi";
        
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        
        $authentication = $this->getServiceLocator()->get("authentication");
        $identity = $authentication->getIdentity();
        $config = $authentication->getConfig();
        
        $controllers = array_keys($config["acl"]["allow"][$identity["role"]]);
        $name = explode("\\", $controllers[0]);
        
        // 將大小寫中間用-分開
        $controller = preg_replace("/(?<!^)([A-Z])/", "-\\1", $name[2]);
        $controller = strtolower($controller);
        $action = (isset($config["acl"]["allow"][$controllers[0]][0]) ? $config["acl"]["allow"][$controllers[0]][0] : "index");
        $url = $this->getServiceLocator()->get("viewhelpermanager")->get("Url");
        $this->redirect()->toUrl($url->__invoke("admin/default", array("controller" => $controller, "action" => $action)));
        
        return $viewModel;
    }
    
    public function loginAction()
    {
        $authorized = new Container("authorized");
        $check = new FieldCheck();
        
        // 預設登入後前往的畫面
        $authentication = $this->getServiceLocator()->get("authentication");
        $controller = null;
        $action = null;
        $query = "";
        
        $view = array(
                "fail" => false,
                "overtime" => $authorized->offsetGet("overtime"),
                "empty" => false,
                "tokenInvalid" => false,
                "tokenEmpty" => false,
        );
        
        if ($this->getRequest()->isPost()) {
            
            try {
                $check->checkToken($this->params()->fromPost("t"));
                $username = $check->checkInput($this->params()->fromPost("username"));
                $password = $check->checkInput($this->params()->fromPost("password"));
                
                $authentication->checkAuth($username, $password);

                if ($authentication->hasIdentity()) {
                    $redirect = new Container("redirect");

                    if ($redirect->offsetExists("controller") && $redirect->offsetExists("action") && $redirect->offsetGet("action") != "not-found") {
                        
                        $name = explode("\\", $redirect->offsetGet("controller"));
                        
                        // 將大小寫中間用-分開
                        $controller = preg_replace("/(?<!^)([A-Z])/", "-\\1", $name[2]);
                        $controller = strtolower($controller);
                        
                        $action = $redirect->offsetGet("action");
                        $query = $redirect->offsetGet("query");
                        
                        if ($query != "" && isset($query)) {
                            $query = "?" . $query;
                        }
                    }
                    $redirect->offsetUnset("controller");
                    $redirect->offsetUnset("action");
                    $redirect->offsetUnset("query");
                
                } else {
                    $view["fail"] = true;
                }
            } catch (FieldEmptyException $e) {
                $view["empty"] = true;
            } catch (TokenNotCorrectException $e) {
                $view["tokenInvalid"] = true;
            } catch (TokenEmptyException $e) {
                $view["tokenEmpty"] = true;
            }
        }
        
        if ($authentication->hasIdentity()) {
            
            $identity = $authentication->getIdentity();
            
            $config = $authentication->getConfig();
            
            if (!isset($controller) || $controller == "") {
                $controllers = array_keys($config["acl"]["allow"][$identity["role"]]);
                $name = explode("\\", $controllers[0]);
                
                // 將大小寫中間用-分開
                $controller = preg_replace("/(?<!^)([A-Z])/", "-\\1", $name[2]);
                $controller = strtolower($controller);
            }
            
            if (!isset($action) || $action == "") {
                $action = (isset($config["acl"]["allow"][$controllers[0]][0]) ? $config["acl"]["allow"][$controllers[0]][0] : "index");
            }
            
            // $this->redirect()->toRoute("admin/default", array("controller" => $controller, "action" => $action));
            $url = $this->getServiceLocator()->get("viewhelpermanager")->get("Url");
            $this->redirect()->toUrl($url->__invoke("admin/default", array("controller" => $controller, "action" => $action)) . $query);
        }
        $view["token"] = $check->createToken("oguma");
        
        $viewModel = new ViewModel($view);
        $viewModel->setTerminal(true);
    
        return $viewModel;
    }
    
    public function logoutAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        
        $authorized = new Container("authorized");
        $authorized->getManager()->destroy();
        
        $redirect = new Container("redirect");
        $redirect->getManager()->destroy();
        
        
        $url = $this->getServiceLocator()->get("viewhelpermanager")->get("Url");
        $response = $this->getResponse();
        $response->setStatusCode(302);
        $response->getHeaders()->addHeaderLine("Location", $url->__invoke("admin/default", array("controller" => "auth", "action" => "login")));
        $response->sendHeaders();
        exit();
        
        return $viewModel;
    }
}
