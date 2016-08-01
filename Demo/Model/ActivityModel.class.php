<?php

namespace Demo\Model;
use Init\Model;

class ActivityModel extends Model
{
  public function __construct(){
      $this->table = strtolower($GLOBALS['db']['prefix'] . substr(substr(__CLASS__,0,-5), -(strlen(substr(__CLASS__,0,-5)) - 11)));
      $this->db = getInstance('Init\Db');
  }
}
