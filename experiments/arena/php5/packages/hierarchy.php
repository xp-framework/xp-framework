<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // {{{ package de~schlund~webservices
  package de~schlund~webservices {
    class ServiceLocator {
    }
  }
  // }}}
    
  // {{{ main
  Reflection::export(new ReflectionClass('de~schlund~webservices~ServiceLocator'));
  
  $instance= new de~schlund~webservices~ServiceLocator();
  var_dump($instance);
  // }}}
?>
