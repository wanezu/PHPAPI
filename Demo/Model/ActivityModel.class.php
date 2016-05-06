<?php

namespace Demo\Model;
use Init\Model;

class ActivityModel extends Model
{
  public function _initialize() {
    $this->table = substr(substr(__CLASS__,0,-5), -(strlen(substr(__CLASS__,0,-5)) - 11));
  }
}
