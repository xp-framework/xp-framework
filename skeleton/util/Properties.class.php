<?php
  class Properties extends Object {
    var
      $filename;
      
    var
      $_data= NULL;
      
    function __construct($filename) {
      $this->filename= $filename;
      Object::__construct();
    }
    
    function _load() {
      if (NULL != $this->_data) return;
      
      $this->_data= parse_ini_file($this->filename, 1);
      if (!is_array($this->_data)) {
        return throw(E_FORMAT_EXCEPTION, $this->filename.' format corrupt!');
      }
    }
    
    function getFirstSection() {
      $this->_load();
      reset($this->_data);
      return key($this->_data);
    }
    
    function getNextSection() {
      $this->_load();
      if (!next($this->_data)) return FALSE;
      return key($this->_data);
    }
    
    function readSection($name, $default= NULL) {
      $this->_load();
      return isset($this->_data[$name]) 
        ? $this->_data[$name] 
        : $default
      ;
    }
    
    function readString($section, $key, $default= '') {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? $this->_data[$section][$key]
        : $default
      ;
    }
    
    function readInteger($section, $key, $default= 0) {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? intval($this->_data[$section][$key])
        : $default
      ;
    }

    function readBool($section, $key, $default= FALSE) {
      $this->_load();
      if (!isset($this->_data[$section][$key])) return $default;
      return (
        strcasecmp('on', $this->_data[$section][$key]) ||
        strcasecmp('yes', $this->_data[$section][$key]) ||
        strcasecmp('true', $this->_data[$section][$key])
      );
    }

  }
?>
