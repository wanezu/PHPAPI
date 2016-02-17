<?php

namespace Demo\Model;
use Init\Model;

class ConfigModel extends Model
{
  public function _initialize() {
    $this->table = 'config';
  }
}
