<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));
date_default_timezone_set("Asia/Taipei");
ini_set("session.cookie_httponly", true);

// Setup autoloading
require 'init_autoloader.php';

Zend\Loader\AutoloaderFactory::factory(array(
    "Zend\\Loader\\StandardAutoloader" => array(
        "namespaces" => array(
            "Tool" => dirname(__DIR__) . "/vendor/tools/Tool/"
        )
    )
));

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
