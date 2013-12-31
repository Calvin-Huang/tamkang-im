<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonEnUS for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /zh_TW/:controller/:action
            'en_US' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/en_US',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EnUS\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                        'param'         => 'test',
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
            'navigation/default' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'navigation/en_us' => 'EnUS\Navigation\EnUSNavigationFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
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
            'EnUS\Controller\Index' => 'EnUS\Controller\IndexController',
            'EnUS\Controller\News' => 'EnUS\Controller\NewsController',
            'EnUS\Controller\File' => 'EnUS\Controller\FileController',
            'EnUS\Controller\Faculty' => 'EnUS\Controller\FacultyController',
            'EnUS\Controller\Collect' => 'EnUS\Controller\CollectController',
            'EnUS\Controller\Institute' => 'EnUS\Controller\InstituteController',
            'EnUS\Controller\Advance' => 'EnUS\Controller\AdvanceController',
            'EnUS\Controller\Admission' => 'EnUS\Controller\AdmissionController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/en_us-layout'           => __DIR__ . '/../view/layout/en_us-layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
