<?php
  class ActiveXObject extends Object {
    var
      $object  = '',
      $_handle = NULL;

    function __construct($class, $server= NULL) {
      $this->object= $class.($server ? '@'.$server : '');
      if (!($this->_handle= com_load($class, $server))) {
        throw(new IllegalArgumentException('Cannot load '.$this->object));
      }
    }
    
    function toString() {
      ob_start();
      com_print_typeinfo($this->_handle, NULL, FALSE);
      $buffer= ob_get_contents();
      ob_end_clean();
      
      sscanf($buffer, 'class %[^ ] { /* GUID={%[^}]}', $class, $guid);
      return $this->getClassName().'<'.$this->object.'>('.$class.'$'.$guid.')';
    }

    function __destruct() {
      if ($this->_handle) {
        com_release($this->_handle);
        $this->_handle= NULL;
      }
    }
  }
?>
