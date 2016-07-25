<?php
/**
 * 设置基本的配置选项
 */
session_start();
ini_set('debug','Off');

//PHP得到系统分隔符
define('DS',DIRECTORY_SEPARATOR);
defined('ROOT') or define('ROOT',dirname(dirname(__FILE__)));
defined('PHPAPP') or define('PHPAPP',dirname(dirname(__FILE__)) . DS . 'phpapp' . DS);

//引入配置文件
require_once(PHPAPP . 'Common' . DS . 'config.php');
require_once(PHPAPP . 'Common' . DS . 'functions.php');


set_include_path(get_include_path() . PATH_SEPARATOR . ROOT);
set_include_path(get_include_path() . PATH_SEPARATOR . PHPAPP);
function __autoload($class){
  $class = str_replace('\\','/',$class);
  require($class . '.class.php');
}

getInstance('Init\Url',substr($_SERVER['PHP_SELF'], (stripos($_SERVER['PHP_SELF'],'index.php') + 10),strlen($_SERVER['PHP_SELF'])));
// var_dump($GLOBALS);
