<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  
  // {{{ class Test
  class Test {
    protected
      $values = [ 6100 ];

    function method($params= [ ]) {
      return [ __METHOD__, $this->values, $params ];
    }
  }
  
  // {{{ main
  $a= [ 1, 2, 3 ];
  $b= [ 'color' => 'green' ];
  var_dump($a, $b);
  
  $a[]= 4;
  var_dump($a[3], $b['color']);
  
  var_dump(call_user_func([ new Test(), 'method' ]));
  // }}}
?>
