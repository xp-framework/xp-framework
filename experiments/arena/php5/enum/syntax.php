<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // Basic syntax of colors
  enum Color {
    BLACK,
    WHITE,
    BLUE,
    GREEN,
    RED(19),
    YELLOW,
    PURPLE,
    BROWN
  }
  
  echo Reflection::export(new ReflectionClass('Color'));
  
?>
