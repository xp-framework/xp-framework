<?php
  class Properties extends Object {
  
    function Properties() {
      $this->__construct();
    }
    
    function __construct() {
      parent::__construct();
    }
    
    function load($urn) {
      $fd= fopen($urn, 'r');
      while ($str= trim(chop(fgets($fd, 4096)))) {
        list($key, $val)= explode('=', $str, 2);
        if (defined($key)) {
          $result= throw(
            E_FORMAT_EXCEPTION,
            $key.' already defined'
          );
        }
        define($key, $val);
      }
      return $result;
    }
    
    function __destruct() {
      parent::__destruct();
    }
  }
?>
