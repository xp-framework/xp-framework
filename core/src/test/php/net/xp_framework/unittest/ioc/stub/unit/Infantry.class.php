<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.ioc.stub.IUnit',
    'net.xp_framework.unittest.ioc.stub.IWeapon'
  );

  /**
   * Represents an Infantry Unit
   *
   */
  class Infantry extends Object implements IUnit {
    private $weapon= NULL;

    /**
     * Constructor
     *
     * @param  net.xp_framework.unittest.ioc.stub.IWeapon $weapon
     */
    public function __construct(IWeapon $weapon) {
      $this->weapon= $weapon;
    }

    /**
     * {@inheritDoc}
     *
     */
    public function attack($target) {
      return 'Infantry: '.$this->weapon->hit($target);
    }

    /**
     * Getter for weapon
     *
     * @return net.xp_framework.unittest.ioc.stub.IWeapon $weapon
     */
    public function getWeapon() {
      return $this->weapon;
    }
  }
?>
