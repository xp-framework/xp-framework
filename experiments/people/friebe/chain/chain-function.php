<?php
/* This file is part of the XP framework's people's experiments
 *
 * $Id$ 
 */

  function &chain(&$begin) {
  
    // Border case #1: Exception already thrown at entry
    if ($e= xp::registry('exceptions')) return throw($e[key($e)]);
    
    // Iterate over chain
    for ($i= 1; $i < func_num_args(); $i++) {
      
      // Make sure $begin is an object
      if (!is_object($begin)) {
        return throw(new NullPointerException('Call to method '.func_get_arg($i).' on '.xp::typeOf($begin)));
      }
      
      // Check what we're getting:
      $arg= func_get_arg($i);
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
        // * Method call without arguments: "toString()"
      }

      try(); {
        // DEBUG print("Evaluating \$begin->".$arg.";\n");
        eval('$begin= &$begin->'.$arg.';');
      } if (catch('Throwable', $e)) {
        return throw($e);
      }
    }

    return $begin;
  }
?>
