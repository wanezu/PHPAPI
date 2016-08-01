<?php
/**
 * 模型类
 */
namespace Init;
use Init\Db;
class Model
{
  //数据库对象
  protected $db;

  //数据表
  protected $table;

  protected $options = array();
  protected $methods = array('where','order','limit','field');

    /**
    * 构造函数 连接数据库即实例化DB类
    */
    public function _initialize($table)
    {
        if (method_exists($this,'_initialize')) {
            $this->_initialize();
        }
    }

  /**
   * 利用__call方法实现连贯操作
   * @access public
   * @param string $method 方法名称
   * @param array $args  调用参数
   * @return mixed
   */
  public function __call($method,$args)
  {
   $method = strtolower($method);
   $arg = isset($args[0]) ? $args[0]:'';
   if (in_array($method,$this->$method,true)) {
     if ($arg !== '') {
       if ($method == 'where') {
         if ($arg === 'form') {
           $arg = $this->getWhereSql();
         }
         if (isset($this->options[$method]) && ($val = $this->options[$method])) {
           # code...
         }
       }
       $this->options[$method] = $arg;
     }
   }
   return $this;
  }


  /**
   * 获得一个表的列表
   */
  public function getField()
  {
    $sql = 'SHOW COLUMNS FROM ' . $this->table;
    $columns = $this->db->getFields($sql);
    return $columns;
  }

  /**
   * 封装查询语句
   */
  public function getWhereSql()
  {
    $data = array_filter(getFormParams(),'arrayFilterVal');
    $fields = $this->db->getFields();
    $where = '';
    if ($data) {
      $whereArr = null;
      foreach ($data as $key => $value) {
        if (array_key_exists($key,$fields)) {
          $whereArr[] = convertWhere($fields[$key]['type'],$key,$value);
        }
      }
      if ($whereArr) {
        $where = implode(' AND ',$whereArr);
      }
    }
    return $where;
  }

 /**
  * 查询数据集
  */
  public function select()
  {
    $sql = 'SELECT ' . (isset($this->options['field'])?$this->options['field']:'*') . ' FROM ' . $this->table;
    $sql .= !empty($this->options['where'])?' WHERE ' . $this->options['where']:'';
    $sql .= isset($this->options['order'])?' ORDER BY ' . $this->options['order']:'';
    $sql .= isset($this->options['limit'])?' LIMIT ' . $this->options['limit']:'';
    $result = $this->db->execute($sql);
    return $result;
  }

/**
 * 查询一条数据
 */
 public function find()
 {
   $sql = 'SELECT ' . (isset($this->options['fields'])?$this->options['field']:'*') . ' FROM ' . $this->table;
   $sql .= isset($this->options['where'])?' WHERE ' . $this->options['where']:'';
   $result = $this->db->query($sql);
   return $result;
 }

 /**
  * 添加一行数据
  */
  public function add($datas)
  {
    $sql = 'INSERT INTO ' . $this->table;
    $columns = $this->db->getFields();
    $fields = '';
    $values = '';
    foreach ($datas as $key => $value) {
      if (array_key_exists($key,$columns)) {
        if ($fields == '') {
          $fields .= $key;
        } else {
          $fields .= ',' . $key;
        }
        if ($values == '') {
          $values .= '\'' . $value . '\'';
        } else {
          $values .= ',\'' . $value . '\'';
        }
      }
    }
    $sql .= '(' . $fields . ')';
    $sql .= ' VALUES (' . $values . ')';
    if ($this->db->execute($sql)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * 删除
   */
   public function delete()
   {
     $sql = 'DELETE FROM ' . $this->table;
     $sql .= isset($this->options['where'])?' WHERE ' . $this->options['where']:'';
     if ($this->db->execute($sql)) {
       return true;
     } else {
       return false;
     }
   }


 /**
  * 更新数据
  */
  public function update($datas)
  {
    $sql = 'UPDATE ' . $this->table . ' SET ';
    $columns = $this->db->getFields();
    $values = '';
    foreach ($datas as $key => $value) {
      if (array_key_exists($key,$columns)) {
        if ($values == '') {
          if (is_numeric($datas[$key])) {
            $values .= $key . '=' . $datas[$key];
          } else {
            $values .= $key . '=\'' . $datas[$key] . '\'';
          }
        } else {
          if (is_numeric($datas[$key])) {
            $values .= ',' . $key . '=' . $datas[$key];
          } else {
            $values .= ',' . $key . '=\'' . $datas[$key] . '\'';
          }
        }
      }
    }
    $sql .= $values;
    $sql .= isset($this->options['where'])?' WHERE ' . $this->options['where']:'';
    if ($this->db->execute($sql)) {
      return true;
    } else {
      return false;
    }
  }
}
