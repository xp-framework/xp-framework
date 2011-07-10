<?php
/* This class is part of the XP framework
 *
 * $Id: Bike.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses(
    'net.xp_framework.unittest.ioc.helper.Tire',
    'net.xp_framework.unittest.ioc.helper.Vehicle'
  );

  /**
   * Helper class for test cases.
   */
  class Bike extends Object implements Vehicle {
    /**
     * injected tire instance
     *
     * @var  Tire
     */
    public $tire;

    /**
     * Create a new car
     *
     * @param  Tire  $tire
     */
    #[@inject]
    public function setTire(Tire $tire) {
      $this->tire = $tire;
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
