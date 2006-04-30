<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  class GenericMap<K, V> extends Object {
    var
      $elements= array();
      
    function __construct($initial= array()) {
      $this->elements= $initial;
    }
  
    function &put($key, &$value) {
      $this->elements[$key]= &$value;
    }

    function &get($key) {
      return $this->elements[$key];
    }
  }
?>
