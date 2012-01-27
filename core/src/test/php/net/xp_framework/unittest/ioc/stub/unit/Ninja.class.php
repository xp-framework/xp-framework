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
   * Represents a Ninja Unit
   *
   */
  class Ninja extends Object implements IUnit {
    private $weapon= NULL;

    /**
     * Constructor
     *
     * @param  net.xp_framework.unittest.ioc.stub.IWeapon $weapon
     */
    #[@inject(context='stealth')]
    public function __construct(IWeapon $weapon) {
      $this->weapon= $weapon;
    }

    /**
     * {@inheritDoc}
     *
     */
    public function attack($target) {
      return 'Ninja: '.$this->weapon->hit($target);
    }
  }
?>
