<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.ioc.helper.TestNumber');

  /**
   * @purpose  Helper class for test cases.
   */
  #[@singleton]
  class RandomSingleton extends Object implements TestNumber {
    private $number;

    /**
     * constructor
     */
    public function __construct() {
      srand();
      $this->number = rand(0, 5000);
    }

    /**
     * displays a number
     */
    public function display() {
      echo $this->number . "\n";
    }
  }
?>
