<?php

namespace Admin\Navigation;

use Zend\Navigation\Service\DefaultNavigationFactory;

class AdminNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return "admin";
    }
}