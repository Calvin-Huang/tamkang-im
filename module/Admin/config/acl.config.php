<?php

return array(
    'acl' => array(
        'roles' => array(
            // inheritance
            /*
             * 'role' => array(
             *     'parentRole1',
             *     'parentRole2'
             * )
             */
            'standard' => null,
            'teacher' => null,
            'administrator' => null,
        ),
        'resources' => array(
            // controllers
            'Admin\Controller\User',
            'Admin\Controller\Article',
            'Admin\Controller\IndexSlide',
            'Admin\Controller\Collect',
            'Admin\Controller\Institute',
            'Admin\Controller\Advance',
            'Admin\Controller\Teacher',
            'Admin\Controller\TeacherProfile',
            'Admin\Controller\Profile',
            'Admin\Controller\Admission',
            'Admin\Controller\Links',
            'Admin\Controller\LinkTypes',
            'Admin\Controller\Icons'
        ),
        'allow' => array(
            // role
            'standard' => array(
                // controller
                'Admin\Controller\Profile' => null,
                'Admin\Controller\Article' => array(
                    // action
                    // 'add',
                    // 'delete',
                    // 'edit',
                    // 'index',
                ),
                'Admin\Controller\IndexSlide' => null,
                'Admin\Controller\Collect' => null,
                'Admin\Controller\Institute' => null,
                'Admin\Controller\Advance' => null,
                'Admin\Controller\Admission' => null,
                'Admin\Controller\Teacher' => null,
                'Admin\Controller\Links' => null,
                'Admin\Controller\LinkTypes' => null
            ),
            'teacher' => array(
                'Admin\Controller\Profile' => null,
                'Admin\Controller\TeacherProfile' => null,
            ),
            'administrator' => array(
                'Admin\Controller\Profile' => null,
                'Admin\Controller\User' => null,
                'Admin\Controller\Icons' => null
            )
        )
    )
);
?>