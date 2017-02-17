<?php
$GLOBALS = array(
    'db' => array(
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '1234567890',
        'name' => 'ceshi',
        'port' => '3306',
        'prefix' => 'sj_'
    ),
    'class' => array(),
    'url' => array(
        // array('index' => 'Demo/index', 'method' => 'get', 'parameter' => array('id' => '\d+')),
        array('index' => 'Demo/index'),
        array('find' => 'Demo/find'),
    ),
);
