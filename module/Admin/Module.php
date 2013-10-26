<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Admin\View\Helper\NavigationHelper;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get("translator");
        $eventManager        = $e->getApplication()->getEventManager();
        $shareEventManager = $eventManager->getSharedManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        // 未登入的導到登入畫面
        $shareEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function($event){
            $controller = $event->getTarget();
            $controller->layout("layout/adminlayout");
            
            $authentication = $controller->getServiceLocator()->get("authentication");
            $identity = $authentication->getIdentity();
            
            // 設定layout使用者頭像
            $controller->layout()->avatar = $identity["avatar"];
            
            // 設定navigation的acl規則
            $navigation = $controller->getServiceLocator()->get("viewhelpermanager")->get("Navigation");
            $navigation->setAcl($authentication->getAcl());
            $navigation->setRole($identity["role"]);
            
            if (!$authentication->hasIdentity()) {
                
                if ($controller->params()->fromRoute("controller") != "auth" && $controller->params()->fromRoute("action") != "login") {
                    
                    // 紀錄原本位置
                    if ($controller->params()->fromRoute("controller") != "auth" && $controller->params()->fromRoute("action") != "logout") {
                    
                        // 登入後導到原本的位置
                        $redirect = new Container("redirect");

                        $redirect->offsetSet("controller", $controller->params()->fromRoute("controller"));
                        $redirect->offsetSet("action", $controller->params()->fromRoute("action"));
                        $redirect->offsetSet("query", $_SERVER["QUERY_STRING"]);
                    }
                    
                    $url = $controller->getServiceLocator()->get("viewhelpermanager")->get("Url");
                    
                    $response = $event->getResponse();
                    $response->setStatusCode(302);
                    $response->getHeaders()->addHeaderLine("Location", $url->__invoke("admin/default", array("controller" => "auth", "action" => "login")));
                    $response->sendHeaders();
                    exit();
                }
            } else if ($controller->params()->fromRoute("controller") != "Admin\\Controller\\Auth") {
                
                if (!$authentication->getAcl()->isAllowed($identity["role"], $controller->params()->fromRoute("controller"), $controller->params()->fromRoute("action"))) {
                    $response = $event->getResponse();
                    $response->setStatusCode(404);
                }
            }
        }, 100);
    }

    public function getConfig()
    {
        return include __DIR__ . "/config/module.config.php";
    }

    public function getAutoloaderConfig()
    {
        return array(
            "Zend\\Loader\\StandardAutoloader" => array(
                "namespaces" => array(
                    __NAMESPACE__ => __DIR__ . "/src/" . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            "factories" => array()
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            "factories" => array(
                "isAllowed" => function(ServiceManager $helperManager){
                    $helper = new NavigationHelper();
                    return $helper;
                }
            ),
        );
    }
}