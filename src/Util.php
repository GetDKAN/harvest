<?php

namespace Harvest;

class Util
{
  public static function generateHash($item) {
    return hash('sha256', serialize($item));
  }

}