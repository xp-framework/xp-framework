<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // {{{ package lang
  package lang {
  
    interface Throwable { 
      public function printStackTrace();
    }
    
    class Exception extends main~Exception implements Throwable {
      public function printStackTrace($fd= STDERR) {
        fwrite($fd, $this->__toString()."\n");
      }
    }
  }
  // }}}
  
  // {{{ package io
  package io {
    class IOException extends lang~Exception { }
  }
  // }}}
  
  // {{{ void loadPackage(string name)
  //     Loads a package
  function loadPackage($name) {
    throw new io~IOException('Package "'.$name.'" not found');
  }
  // }}}
  
  // {{{ main
  Reflection::export(new ReflectionClass('io~IOException'));
  
  try {
    loadPackage('binford');
  } catch (lang~Exception $e) { // Bad style, but OK for demo purposes
    $e->printStackTrace();
  }
  // }}}
?>
