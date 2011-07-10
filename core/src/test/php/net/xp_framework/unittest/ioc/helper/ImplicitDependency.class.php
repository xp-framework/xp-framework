<?php
/* This class is part of the XP framework
 *
 * $Id: ImplicitDependency.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('net.xp_framework.unittest.ioc.helper.Goodyear');

  /**
   * Helper class for test cases.
   */
  class ImplicitDependency extends Object {
    /**
     * instance from constructor injection
     *
     * @var  Goodyear
     */
    protected $goodyearByConstructor;
    /**
     * instance from setter injection
     *
     * @var  Goodyear
     */
    protected $goodyearBySetter;

    /**
     * constructor
     *
     * @param  Goodyear  $goodyear
     */
    #[@inject]
    public function __construct(Goodyear $goodyear) {
      $this->goodyearByConstructor = $goodyear;
    }

    /**
     * setter
     *
     * @param  Goodyear  $goodyear
     */
    #[@inject]
    public function setGoodyear(Goodyear $goodyear) {
      $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from constructor injection
     *
     * @return  Goodyear
     */
    public function getGoodyearByConstructor() {
      return $this->goodyearByConstructor;
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
