<?php
/**
 * 数据库连接类
 */

 namespace Lib;

 Class Db {
   //用于存储数据库链接
   protected $conn;

   //连接数据库
   public function __construct(){
     $this->connect();
   }

   //连接数据库
   public function connect(){
     $db = $GLOBALS['db'];
     $this->conn = mysqli_connect($db['host'] . ':' . $db['port'] , $db['user'] , $db['password'] , $db['name']);
     $this->query('set names utf8');
   }

  //执行sql语句
  public function execute($sql)
  {
    if ($this->conn != null) {
      $result = $this->query($sql);
    }
    if (!$result) {
      return false;
    } elseif (isset($result->num_rows)) {
      if ($result->num_rows == 0) {
        return array();
      }elseif ($result->num_rows >= 1) {
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
          $rows[] = $row;
        }
        return $rows;
      }else {
        return true;
      }
    }
  }


  //执行sql语句
  public function query($sql){
    if ($this->conn != null) {
      Log::write($sql);
      $result = mysqli_query($this->conn,$sql);
    }
    return $result;
    // if (!$result) {
    //   return false;
    // } else {
    //   $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    //   return $row;
    // }
  }

  //查询数据表的字段
  public function getFields($sql){
    if ($this->conn != null) {
      $result = $this->query($sql);
      while ($result !== false && ($row = mysqli_fetch_array($result)) != null) {
        $columns[] = $row;
      }

      foreach ($columns as $k => $field) {
        $fields[$field['Field']] = array(
          'name' => $field['Field'],
          'type' => preg_replace('/\(\d+\)/','',$field['Type']),
          'notnull' => (strtolower($field['Null']) == 'yes'),
          'default' => $field['Default'],
          'primary' => (strtolower($field['Key']) == 'Pri'),
          'autoinc' => (strtolower($field['Extra']) == 'auto_increment')
        );
      }
      return $fields;
    }
  }

  //析构方法
  public function __destruct(){
    $this->conn = null;
  }
}
