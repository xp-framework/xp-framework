<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  // {{{ class IOException
  class IOException extends Exception {
  }
  // }}} 

  // {{{ class IllegalArgumentException
  class IllegalArgumentException extends Exception {
  }
  // }}} 

  // {{{ class Socket
  class Socket {
    public function connect() throws IllegalArgumentException, IOException {
      // ...
    }
  } 
  // }}}
  
  // {{{ main
  $method= new ReflectionMethod('Socket', 'connect');
  Reflection::export($method);
  
  foreach ($method->getExceptionTypes() as $ex) {
    Reflection::export($ex);
  }
  // }}}
?>
