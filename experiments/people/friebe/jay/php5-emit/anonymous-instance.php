<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  
  require('__xp__.php');
  
  // {{{ Original
  // interface Comparator {
  //   public bool compare($a, $b); 
  // }
  //
  // echo new Comparator() { 
  //   public bool compare($a, $b) { 
  //     return strcmp($a, $b);
  //   }
  // }->compare('Hello', 'World');
  // }}}

  // {{{ Generated version
  interface Comparator {
    function compare($a, $b); 
  }
  
  echo xp::instance('Comparator', array(), '{
    function compare($a, $b) { 
      return strcmp($a, $b); 
    }
  }')->compare('Hello', 'World');
  // }}}
?>
