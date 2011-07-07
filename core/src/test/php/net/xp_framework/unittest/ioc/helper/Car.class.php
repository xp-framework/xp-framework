<?php
/* This class is part of the XP framework
 *
 * $Id: Car.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses(
    'net.xp_framework.unittest.ioc.helper.Tire',
    'net.xp_framework.unittest.ioc.helper.Vehicle'
  );

  /**
   * @purpose  Helper class for test cases.
   */
  class Car extends Object implements Vehicle {
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
    #[@Inject]
    public function __construct(Tire $tire) {
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
