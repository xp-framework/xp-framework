<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  enum Operation {
    plus { 
      function evaluate($x, $y) { return $x + $y; } 
    },
    minus { 
      function evaluate($x, $y) { return $x - $y; } 
    },
    times { 
      function evaluate($x, $y) { return $x * $y; } 
    },
    divided_by { 
      function evaluate($x, $y) { return $x / $y; } 
    };
  }

  $x= 2;
  $y= 4;
  foreach (Operation::values() as $op) {
    printf("%d %s %s = %.1f\n", $x, $op->name, $y, $op->evaluate($x, $y));
  }
?>
