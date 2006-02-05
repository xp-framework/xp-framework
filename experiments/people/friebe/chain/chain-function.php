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

      try(); {
        eval('$begin= &$begin->'.func_get_arg($i).';');
      } if (catch('Throwable', $e)) {
        return throw($e);
      }
    }

    return $begin;
  }
?>
