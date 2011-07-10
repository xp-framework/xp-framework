<?php
/* This class is part of the XP framework
 *
 * $Id: Goodyear.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('net.xp_framework.unittest.ioc.helper.Tire');

  /**
   * Helper class for test cases.
   */
  class Goodyear extends Object implements Tire {
    /**
     * rotates the tires
     *
     * @return  string
     */
    public function rotate() {
      return "I'm driving with Goodyear tires.";
    }
  }
?>
