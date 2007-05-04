<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'info.binford6100';

  uses('util.Date');

  class info·binford6100·Date extends Date {
  
    function toString() {
      return 'More Power: '.parent::toString();
    }
  }
?>
