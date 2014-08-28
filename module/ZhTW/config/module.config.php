<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonZhTW for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'ZhTW\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /zh_TW/:controller/:action
            'zh_TW' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/zh_TW',
                    'defaults' => array(
                        '__NAMESPACE__' => 'ZhTW\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'zh_TW',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ZhTW\Controller\Index' => 'ZhTW\Controller\IndexController',
            'ZhTW\Controller\News' => 'ZhTW\Controller\NewsController',
            'ZhTW\Controller\File' => 'ZhTW\Controller\FileController',
            'ZhTW\Controller\Faculty' => 'ZhTW\Controller\FacultyController',
            'ZhTW\Controller\Undergraduate' => 'ZhTW\Controller\UndergraduateController',
            'ZhTW\Controller\Graduate' => 'ZhTW\Controller\GraduateController',
            'ZhTW\Controller\Emba' => 'ZhTW\Controller\EmbaController',
            'ZhTW\Controller\Admission' => 'ZhTW\Controller\AdmissionController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'zh-tw/index/index' => __DIR__ . '/../view/zh-tw/index/index.phtml',
            'zh-tw/index/index.template' => __DIR__ . '/../view/zh-tw/index/index.template.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ),
);
