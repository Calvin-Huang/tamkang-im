<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    // ...
    // All navigation-related configuration is collected in the 'navigation' key
    'navigation' => array(
        // The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
        // And Second Factory uses 'admin'
        'admin' => array(
            // And finally, here is where we define our page hierarchy
            array(
                'id' => 'profile-manage',
                'label' => '帳戶設定',
                'route' => 'admin/default',
                'controller' => 'profile',
                'resource' => 'Admin\Controller\Profile',
            ),
            array(
                'id' => 'user-manage',
                'label' => '使用者管理',
                'route' => 'admin/default',
                'controller' => 'user',
                'resource' => 'Admin\Controller\User'
            ),
            array(
                'id' => 'teacher-manage',
                'label' => '教師管理',
                'route' => 'admin/default',
                'controller' => 'teacher',
                'resource' => 'Admin\Controller\Teacher',
                'pages' => array(
                    'sort' => array(
                        'label' => '設定顯示順序',
                        'route' => 'admin/default',
                        'controller' => 'teacher'
                    ),
                    'book-type' => array(
                        'label' => '設定教師相關資料種類',
                        'route' => 'admin/default',
                        'controller' => 'teacher',
                        'action' => 'booktype'
                    ),
                    'teacher-hire' => array(
                        'label' => '設定教師職稱',
                        'route' => 'admin/default',
                        'controller' => 'teacher',
                        'action' => 'hire'
                    )
                )
            ),
            array(
                'id' => 'article-manage',
                'label' => '文章',
                'route' => 'admin/default',
                'controller' => 'article',
                'resource' => 'Admin\Controller\Article',
                'pages' => array(
                    'article' => array(
                        'label' => '文章管理',
                        'route' => 'admin/default',
                        'controller' => 'article'
                    ),
                    'type' => array(
                        'label' => '設定文章類別',
                        'route' => 'admin/default',
                        'controller' => 'article',
                        'action' => 'articletype'
                    )
                )
            ),
            array(
                'id' => 'slide-manage',
                'label' => 'slide管理',
                'route' => 'admin/default',
                'controller' => 'index-slide',
                'resource' => 'Admin\Controller\IndexSlide'
            ),
            array(
                'id' => 'collect-manage',
                'label' => '大學部管理',
                'route' => 'admin/default',
                'controller' => 'collect',
                'resource' => 'Admin\Controller\Collect',
                'pages' => array(
                    'introduce' => array(
                        'label' => '介紹管理',
                        'route' => 'admin/default',
                        'controller' => 'collect',
                    ),
                    'type' => array(
                        'label' => '設定介紹類別',
                        'route' => 'admin/default',
                        'controller' => 'collect',
                        'action' => 'type'
                    ),
                )
            ),
            array(
                'id' => 'institute-manage',
                'label' => '碩士班管理',
                'route' => 'admin/default',
                'controller' => 'institute',
                'resource' => 'Admin\Controller\Institute',
                'pages' => array(
                    'introduce' => array(
                        'label' => '介紹管理',
                        'route' => 'admin/default',
                        'controller' => 'institute'
                    ),
                    'type' => array(
                        'label' => '設定介紹類別',
                        'route' => 'admin/default',
                        'controller' => 'institute',
                        'action' => 'type'
                    ),
                )
            ),
            array(
                'id' => 'advance-manage',
                'label' => '碩士在職專班管理',
                'route' => 'admin/default',
                'controller' => 'advance',
                'resource' => 'Admin\Controller\Advance',
                'pages' => array(
                    'introduce' => array(
                        'label' => '介紹管理',
                        'route' => 'admin/default',
                        'controller' => 'advance'
                    ),
                    'type' => array(
                        'label' => '設定介紹類別',
                        'route' => 'admin/default',
                        'controller' => 'advance',
                        'action' => 'type'
                     ),
                )
            ),
            array(
                'id' => 'admission-manage',
                'label' => '招生資訊管理',
                'route' => 'admin/default',
                'controller' => 'admission',
                'action' => 'index',
                'resource' => 'Admin\Controller\Admission',
                'pages' => array(
                    'introduce' => array(
                            'label' => '介紹管理',
                            'route' => 'admin/default',
                            'controller' => 'admission'
                    ),
                    'type' => array(
                            'label' => '設定介紹類別',
                            'route' => 'admin/default',
                            'controller' => 'admission',
                            'action' => 'type'
                    ),
                )
            ),
            array(
                'id' => 'teacher-info',
                'label' => '歷程顯示資料',
                'route' => 'admin/default',
                'controller' => 'teacher-profile',
                'resource' => 'Admin\Controller\TeacherProfile',
            ),
        ),
        'default' => array(
            // And finally, here is where we define our page hierarchy
            array(
                'id' => 'news',
                'label' => '消息公告',
                'route' => 'zh_TW/default',
                'controller' => 'news',
            ),
            array(
                'id' => 'faculty',
                'label' => '教師陣容',
                'route' => 'zh_TW/default',
                'controller' => 'faculty',
                'action' => 'index'
            ),
            array(
                'id' => 'undergraduate',
                'label' => '大學部',
                'route' => 'zh_TW/default',
                'controller' => 'undergraduate',
                'action' => 'index'
            ),
            array(
                'id' => 'graduate',
                'label' => '碩士班',
                'route' => 'zh_TW/default',
                'controller' => 'graduate',
                'action' => 'index'
            ),
            array(
                'id' => 'emba',
                'label' => '碩士在職專班',
                'route' => 'zh_TW/default',
                'controller' => 'emba',
                'action' => 'index'
            ),
            // array(
            //     'id' => 'download',
            //     'label' => '文件查找',
            //     'route' => 'application/default',
            //     'controller' => 'file',
            //     'action' => 'search'
            // ),
            array(
                'id' => 'admission',
                'label' => '招生簡介',
                'route' => 'zh_TW/default',
                'controller' => 'admission',
                'action' => 'index'
            ),
            array(
                'label' => 'English',
                'uri' => 'http://163.13.200.22/en_index.html'
            ),
            // array(
            //     'label' => '<i class="fa fa-globe"></i>',
            //     'route' => 'en_US/default',
            //     'controller' => 'index',
            //     'action' => 'index',
            //     'pages' => array(
            //         array(
            //             'label' => '<i class="fa fa-language fa-fw"></i> English Version',
            //             'route' => 'en_US/default',
            //             'controller' => 'index',
            //             'action' => 'index'
            //         )
            //     )
            // ),
        ),
        'en_us' => array(
            // And finally, here is where we define our page hierarchy
            array(
                'id' => 'news_en_US',
                'label' => 'News',
                'route' => 'en_US/default',
                'controller' => 'news',
            ),
            array(
                'id' => 'faculty_en_US',
                'label' => 'Faculty',
                'route' => 'en_US/default',
                'controller' => 'faculty',
                'action' => 'index'
            ),
            array(
                'id' => 'undergraduate_en_US',
                'label' => 'Undergraduate',
                'route' => 'en_US/default',
                'controller' => 'undergraduate',
                'action' => 'index'
            ),
            array(
                'id' => 'graduate_en_US',
                'label' => 'Graduate',
                'route' => 'en_US/default',
                'controller' => 'graduate',
                'action' => 'index'
            ),
            array(
                'id' => 'emba_en_US',
                'label' => 'EMBA',
                'route' => 'en_US/default',
                'controller' => 'emba',
                'action' => 'index'
            ),
            // array(
            //     'id' => 'download',
            //     'label' => '文件查找',
            //     'route' => 'application/default',
            //     'controller' => 'file',
            //     'action' => 'search'
            // ),
            array(
                'id' => 'admission_en_US',
                'label' => 'Admission',
                'route' => 'en_US/default',
                'controller' => 'admission',
                'action' => 'index'
            ),
            array(
                    'label' => '<i class="fa fa-globe"></i>',
                    'route' => 'zh_TW/default',
                    'controller' => 'index',
                    'action' => 'index',
                    'pages' => array(
                            array(
                                    'label' => '<i class="fa fa-language fa-fw"></i> 中文版本',
                                    'route' => 'zh_TW/default',
                                    'controller' => 'index',
                                    'action' => 'index'
                            )
                    )
            ),
        ),
    ),
    'teacher_system_url' => 'http://teacher.tku.edu.tw'
);
