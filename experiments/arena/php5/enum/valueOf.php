<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
 
  enum Day { Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday }

  foreach (range(Day::Monday, Day::Sunday) as $day) {
    echo $day, ': ', Day::valueOf($day)->name, "\n";
  }
  
?>
