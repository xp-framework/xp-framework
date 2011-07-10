<?php
/* This class is part of the XP framework
 *
 * $Id: Goodyear.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('net.xp_framework.unittest.ioc.helper.Person');

  /**
   * @purpose  Helper class for test cases.
   */
  class Developers extends Object {
    public
      $mikey,
      $schst;

    /**
     * Setter method with Named() annotation
     *
     * @param  Person  $schst
     */
    #[@inject, @named('schst')]
    public function setSchst(Person $schst) {
      $this->schst = $schst;
    }

    /**
     * Setter method without Named() annotation
     *
     * @param  Person  $schst
     */
    #[@inject]
    public function setMikey(Person $mikey) {
      $this->mikey = $mikey;
    }
  }
?>
