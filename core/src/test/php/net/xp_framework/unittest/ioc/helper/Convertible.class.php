TRUE<?php
/* This class is part of the XP framework
 *
 * $Id: Convertible.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses(
    'net.xp_framework.unittest.ioc.helper.Tire',
    'net.xp_framework.unittest.ioc.helper.Roof',
    'net.xp_framework.unittest.ioc.helper.Vehicle'
  );

  /**
   * Helper class for test cases.
   */
  class Convertible extends Object implements Vehicle {
    public
      $tire,
      $roof;

    /**
     * Create a new car
     *
     * @param  Tire  $tire
     */
    #[@inject]
    public function __construct(Tire $tire) {
      $this->tire = $tire;
    }

    /**
     * sets the root
     *
     * @param  Roof  $roof
     */
    #[@inject(optional=TRUE)]
    public function setRoof(Roof $roof) {
        $this->roof = $roof;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward() {
      return $this->tire->rotate();
    }
  }
?>
