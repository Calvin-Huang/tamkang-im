<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'admin' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'login',
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
            'navigation/admin' => 'Admin\Navigation\AdminNavigationFactory',
        ),
        'services' => array(
            'authentication' => new Admin\Event\Authentication(),
        )
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
            'Admin\Controller\Auth' => 'Admin\Controller\AuthController',
            'Admin\Controller\User' => 'Admin\Controller\UserController',
            'Admin\Controller\Teacher' => 'Admin\Controller\TeacherController',
            'Admin\Controller\Article' => 'Admin\Controller\ArticleController',
            'Admin\Controller\IndexSlide' => 'Admin\Controller\IndexSlideController',
            'Admin\Controller\Collect' => 'Admin\Controller\CollectController',
            'Admin\Controller\Institute' => 'Admin\Controller\InstituteController',
            'Admin\Controller\Advance' => 'Admin\Controller\AdvanceController',
            'Admin\Controller\Profile' => 'Admin\Controller\ProfileController',
            'Admin\Controller\TeacherProfile' => 'Admin\Controller\TeacherProfileController',
            'Admin\Controller\Admission' => 'Admin\Controller\AdmissionController',
            'Admin\Controller\Links' => 'Admin\Controller\LinksController',
            'Admin\Controller\LinkTypes' => 'Admin\Controller\LinkTypesController',
            'Admin\Controller\Icons' => 'Admin\Controller\IconsController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'template/navigation'     => __DIR__ . '/../view/template/navigation.phtml',
            'template/breadcrumb'     => __DIR__ . '/../view/template/breadcrumb.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ), 
);
