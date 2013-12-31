<?php

namespace EnUS\Navigation;

use Zend\Navigation\Service\DefaultNavigationFactory;

class EnUSNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return "en_us";
    }
}