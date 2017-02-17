<?php
namespace Demo\Controller;

use Init\Controller;
use Init\Response;

class DemoController extends Controller
{
    // private $config_model;
    // public function _initialize(){
    //   $this->config_model = M('Config');
    // }

    public function index()
    {
        $code = 200;
        $message = '成功';
        $activity = M('Activity');
        $config = $activity->select();
        $time = 3600;
        $data = Response::show($code, $message, $config, $type = 'json', $cache = 'index', $time);
        // $data = Response::getcache('index','xml');
        echo $data;
    }

    public function find()
    {
        $id = I('get.id', '', 'htmlspecialchars');
        $activity = M('Activity');
        $config = $activity->select();
        var_dump($config);
    }
}
