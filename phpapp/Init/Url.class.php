<?php
/**
 * 数据库连接类
 */

 namespace Init;

 Class Url {
   //用于存储数据库链接
   protected $url;

   //连接数据库
   public function __construct($url){
      $this->url = $url;
      $this->urltesting();
   }

   //连接数据库
   public function urltesting(){
     $urlconfig = $GLOBALS['url'];
     foreach ($urlconfig as $key => $value) {
       foreach ($value as $k => $v) {
        if ($this->url == $k) {
          $arr = explode("/",$v);
          $d = getInstance(APP_NAME . '\Controller\\' . $arr[0] . 'Controller');
          $d->$arr[1]();
        }
       }
     }
   }
}
