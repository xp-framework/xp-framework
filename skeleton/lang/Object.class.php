<?php
  class Object {
    function Object($params= NULL) {
      $this->__construct($params);
    }
    
    function __construct($params= NULL) {
      if (NULL == $params) return;
      foreach ($params as $key=> $val) $this->$key= $val;
    }
    
    function __destruct() {
      unset($this);
    }
  }
