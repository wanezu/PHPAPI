<?php
namespace Demo\Controller;
use Init\Controller;
use Init\Response;

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
    $data = Response::show($code,$message,$config,$type = 'xml',$cache = 'index',$time);
    // $data = Response::getcache('index','xml');
    echo $data;
  }

  public function find(){
    echo get_client_ip();
  }
}
