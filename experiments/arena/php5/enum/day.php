<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  enum Day {
    Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday
  }
  
  // {{{ main
  echo "Workdays:\n";
  foreach (range(Day::Monday, Day::Friday) as $number) {
    echo $number, ': ', Day::valueOf($number)->name, "\n";
  }
  // }}}
?>
