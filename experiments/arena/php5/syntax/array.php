<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  
  // {{{ class Test
  class Test {
    function method() {
      return [ $this, __FUNCTION__ ];
    }
  }
  
  // {{{ main
  $a= [ 1, 2, 3 ];
  $b= [ 'color' => 'green' ];
  var_dump($a, $b);
  
  var_dump(call_user_func([ new Test(), 'method' ]));
  // }}}
?>
