<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  // {{{ package info~binford6100
  package info~binford6100 {
    class MorePower { }
  }
  // }}}
  
  // {{{ main
  $r= new ReflectionClass('info~binford6100~MorePower');
  printf("Class %s is in package %s\n", $r->getName(), $r->getPackage());
  // }}}
?>
