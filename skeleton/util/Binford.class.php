<?php
  class Binford extends Object { 
    var $poweredBy= 6100;
    
    function __construct($params= NULL) {
      Object::__construct($params);  
    }
    
    function setPoweredBy($p) {
      if (!($x= log10($p / 6.1)) || (floor($x) != $x)) {
        return throw(E_ILLEGAL_ARGUMENT_EXCEPTION, $p.' not allowed');
      }
      $this->poweredBy= $p;
    }
    
    function getPoweredBy() {
      return $this->poweredBy;
    }
    
    function getHeader() {
      return 'X-Binford: '.$this->poweredBy.' (more power)';
    }
  }
?>
