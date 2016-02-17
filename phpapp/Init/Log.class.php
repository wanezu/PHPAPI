<?php
namespace Init;
//记录日志
class Log
{
  const LOGFILE = 'curr.log';//建立一个常量，代表日志文件的名称

  //写日志的
  public static function write($cont)
  {
    $cont .= '  ' . date('Y-m-d H:i:s',time()) . "\r\n";
    //判断是否备份
    $log = self::isBak();//计算出日志文件的目录

    $fh = fopen($log,'ab');
    fwrite($fh,$cont);
    fclose($fh);
  }

  //备份日志
  public static function bak()
  {
    $log = ROOT . DS . 'data' . DS . 'log' . DS . self::LOGFILE;
    $bak = ROOT . DS . 'data' . DS . 'log' . DS . date('ymd') . mt_rand(10000,99999) . '.bak';
    return rename($log,$bak);
  }

  //读取并判断日至文件的大小
  public static function isBak()
  {
    $log = ROOT . DS . 'data' . DS . 'log' . DS . self::LOGFILE;
    if (!file_exists($log)) {
      touch($log);
      return $log;
    }

    clearstatcache(true,$log);
    $size = filesize($log);
    if ($size <= 1024 * 1024) {
      return $log;
    }

    //这一步说明日志文件大于1M
    if (!self::Back()) {
      return $log;
    } else {
      touch($log);
      return $log;
    }
  }















}
