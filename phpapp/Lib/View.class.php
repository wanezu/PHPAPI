<?php
/**
 * 视图类
 */
namespace Lib;

/**
 *
 */
class View
{
  protected $vars = array();

  //视图变量赋值
  public function set($key,$value = null){
    $this->vars[$key] = $value;
  }

  //渲染视图文件
  public function render($name = null)
  {
    extract($this->vars,EXTR_SKIP);
    ob_start();
    ob_implicit_flush(0);
    require_once(ROOT.$name);
    return ob_get_clean();
  }
}
