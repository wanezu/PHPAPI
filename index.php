<?php
ini_set('debug','On');
require_once(__DIR__ . '/phpapp/api.php');

$d = getInstance('Demo\Controller\DemoController');
$d->find();
