<?php
/* This file is part of the XP framework's people's experiments
 *
 * $Id$ 
 */

  function &chain(&$begin) {
  
    // Border case #1: Exception already thrown at entry
    if (xp::registry('exceptions')) return xp::null();
    
    // Iterate over chain
    for ($i= 1; $i < func_num_args(); $i++) {
      
      // Derive context
      $arg= func_get_arg($i);
      if (is_array($begin)) {

        // Check what we're getting:
        if ('[' == $arg{0} and ']' == $arg{strlen($arg) - 1}) {

          // * Constant array offset: "[1]"
          $key= substr($arg, 1, -1);
        } else {
        
          // * Dynamic array offset: "[", $offset, "]"
          $key= func_get_arg(++$i);
          $i++;
        }
        $begin= &$begin[$key];
      } else if (is_object($begin)) {

        // Check what we're getting:
        if ('(' == $arg[strlen($arg) - 1]) {

          // * Method call with argument: "getCategory(", $arg, ")"
          $args= array();
          $j= 0;
          while (')' != func_get_arg(++$i)) {
            $args[]= func_get_arg($i);
            $arg.= '$args['.$j++.'], ';
          }
          $arg= substr($arg, 0, -2).')';
        } else {
          // * Method call without arguments: "toString()" OR
          // * Member read: "value"
        }

        try(); {
          eval('$begin= &$begin->'.$arg.';');
        } if (catch('Throwable', $e)) {
          return throw($e);
        }
      } else {
        return throw(new NullPointerException('Call to method '.$arg.' on '.xp::typeOf($begin)));
      }
    }

    return $begin;
  }
?>
