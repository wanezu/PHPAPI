<?php

namespace Demo\Model;
use Lib\Model;

class ConfigModel extends Model
{
  public function _initialize() {
    $this->table = 'config';
  }
}
