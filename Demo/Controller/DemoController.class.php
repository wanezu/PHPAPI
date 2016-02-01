<?php
namespace Demo\Controller;
use Lib\Controller;
use Lib\Response;

class DemoController extends Controller {
  private $config_model;
  public function _initialize(){
    $this->config_model = getInstance('Demo\Model\ConfigModel');
  }

  public function index()
  {
    $code = 200;
    $message = '成功';
    $config = $this->config_model->getFields();
    $time = 3600;
    // $data = Response::show($code,$message,$config,$type = 'xml',$cache = 'index',$time);
    $data = Response::getcache('index','txt');
    echo $data;
  }

  public function find(){
    echo "急促恶化富而后";
  }
}
