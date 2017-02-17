<?php
ini_set('debug', 'On');
define('APP_NAME', 'Demo');
require_once(__DIR__ . '/phpapp/api.php');


$d = getInstance('Demo\Controller\DemoController');
// var_dump($GLOBALS);
$d->index();
// echo $_SERVER['PHP_SELF'];
// echo $_SERVER["QUERY_STRING"];

// echo substr($_SERVER['PHP_SELF'], (stripos($_SERVER['PHP_SELF'],'index.php') + 10),strlen($_SERVER['PHP_SELF']));
