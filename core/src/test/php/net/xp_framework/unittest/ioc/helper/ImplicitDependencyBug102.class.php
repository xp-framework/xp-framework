<?php
/* This class is part of the XP framework
 *
 * $Id: ImplicitDependencyBug102.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('net.xp_framework.unittest.ioc.helper.Goodyear');

  /**
   * @purpose  Helper class for test cases.
   * @link     http://stubbles.net/ticket/102
   */
  class ImplicitDependencyBug102 extends Object {
    /**
     * instance from setter injection
     *
     * @var  Goodyear
     */
    protected $goodyearBySetter;

    /**
     * setter
     *
     * @param  Goodyear  $goodyear
     */
    #[@Inject]
    public function setGoodyear(Goodyear $goodyear) {
      $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  Goodyear
     */
    public function getGoodyearBySetter() {
      return $this->goodyearBySetter;
    }
  }
?>
