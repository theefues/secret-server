<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");

require_once PROJECT_ROOT_PATH . "inc/config.php";

function apiControllerRegister($class_name) {
    $filename = PROJECT_ROOT_PATH . "Controller/Api/" . $class_name . '.php';
    if(is_readable($filename)) {
        require_once $filename;
    } 
}

function modelRgister($class_name) {
    $filename = PROJECT_ROOT_PATH . "Model/" . $class_name . '.php';
    if(is_readable($filename)) {
        require_once $filename;
    } 
}

function serviceRegister($class_name) {
    $filename = PROJECT_ROOT_PATH . "Service/" . $class_name . '.php';
    if(is_readable($filename)) {
        require_once $filename;
    }  
}

spl_autoload_register('apiControllerRegister');
spl_autoload_register('modelRgister');
spl_autoload_register('serviceRegister');
