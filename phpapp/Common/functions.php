<?php

//创建获取实例
function getInstance($class_name){
  if (!isset($GLOBALS['class'][$class_name])) {
    $GLOBALS['class'][$class_name] = new $class_name();
    return $GLOBALS['class'][$class_name];
  } else {
    return $GLOBALS['class'][$class_name];
  }
}

//
