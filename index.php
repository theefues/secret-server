<?php
require __DIR__ . "/src/inc/bootstrap.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (!isset($uri[2]) || !isset($uri[3])) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

switch($uri[2]) {
    case "secret": 
        $objFeedController = new SecretController();
        $strMethodName = $uri[3] . 'Action';
        $objFeedController->{$strMethodName}();
    break;
    default: 
        header("HTTP/1.1 404 Not Found");
        exit();
    break;
}


?>