<?php
/**
 * 控制器基类
 */
namespace Init;

class Controller
{
  //视图实例化
  protected $view;
  /**
   * 构造函数
   */
  public function __construct()
  {
    $this->view = getInstance('Init\View');
    if (method_exists($this,'_initialize')) {
      $this->_initialize();
    }
  }

  //视图变量赋值
  public function assign($name,$data = null)
  {
    if (is_array($name)) {
      foreach ($name as $key => $value) {
        $this->view->set($key,$value);
      }
    } else {
      $this->view->set($name,$data);
    }
  }
    //视图渲染
    public function display($name = null)
    {
      echo $this->view->render($name);
    }

    //获取表单提交数据
    protected function getFormParams()
    {
      $params = null;
      evel("\$params = \$_{$_SERVER['REQUEST_METHOD']};");
      return $params;
    }
}
