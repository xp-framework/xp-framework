<?php
/* This class is part of the XP framework
 *
 * $Id: ImplicitOptionalDependency.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('net.xp_framework.unittest.ioc.helper.Goodyear');

  /**
   * @purpose  Helper class for test cases.
   */
  class ImplicitOptionalDependency extends Object {
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
    #[@Inject(optional=true)]
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
