<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Model\IndexSlideModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $sharedManager = $eventManager->getSharedManager();
        // $sharedManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, array($this, 'layoutChanger'));
        // $sharedManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, array($this, 'attachSlide'));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function layoutChanger($e)
    {
        $controller = $e->getTarget();
        if ($controller->params()->fromRoute("controller") == "Application\\Controller\\Index") {
            $controller->layout("layout/index-layout");
        }
    }
    
    public function attachSlide($e)
    {
        $indexSlideModel = new IndexSlideModel();
        
        $controller = $e->getTarget();
        $controller->layout()->slides = $indexSlideModel->listIndexSlide();
    }
}
