<?php

namespace Lib;
//生成缓存文件
class File
{

  /**
   * 生成缓存文件
   * @param string $key 表示文件名称
   * @param array $value 数据
   * @param integer $cacheTime 数据文件存储时间
   */
  public static function cacheDate($key,$type,$value = '',$cacheTime = 0)
  {
    $filename = ROOT . DS . 'data' . DS . 'files' . DS . $key . '.' . $type;
    if ($value !== '') {
      if (is_null($value)) {
        return @unlink($filename);
      }
      $dir = dirname($filename);
      if (!is_dir($dir)) {
        mkdir($dir,0777);
      }

      $cacheTime = sprintf('%011d',$cacheTime);
      return file_put_contents($filename,$cacheTime . $value);
    }

    if (!is_file($filename)) {
      return FALSE;
    }

    $contents = file_get_contents($filename);
    $cacheTime = (int)substr($contents,0,11);
    $value = substr($contents,11);
    if ($cacheTime != 0 && ($cacheTime + filemtime($filename) < time())) {
      unlink($filename);
      return FALSE;
    }

    return $value;
  }
}
