<?php

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Suppress warning is_readable() [function.is-readable]: open_basedir restriction in effect. in prod
set_include_path(
    APPLICATION_PATH.'/../library'.PATH_SEPARATOR.
    APPLICATION_PATH.'/../library/Zend'
);

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Define if the application sends notifications
defined('SEND_NOTIFICATION')
|| define('SEND_NOTIFICATION', (getenv('SEND_NOTIFICATION') ? getenv('SEND_NOTIFICATION') : 'all'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
    ->run();
