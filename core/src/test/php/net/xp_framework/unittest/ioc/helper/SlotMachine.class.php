<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.ioc.helper.TestNumber');

  /**
   * Helper class for test cases.
   */
  class SlotMachine extends Object {
    public
      $number1,
      $number2;

    /**
     * Set number 1
     *
     * @param  net.xp_framework.unittest.ioc.helper.TestNumber  $number
     */
    #[@inject]
    public function setNumber1(TestNumber $number) {
      $this->number1 = $number;
    }

    /**
     * Set number 2
     *
     * @param  net.xp_framework.unittest.ioc.helper.TestNumber  $number
     */
    #[@inject]
    public function setNumber2(TestNumber $number) {
      $this->number2 = $number;
    }
  }
?>
