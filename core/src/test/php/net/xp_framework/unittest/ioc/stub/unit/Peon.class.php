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
  class Peon extends Object implements IUnit {
    protected $weapon= NULL;

    /**
     * {@inheritDoc}
     *
     */
    public function attack($target) {
      if (NULL === $this->weapon) {
        return 'Peon: I have no weapon so '.$target.' got away';
      }
      return 'Peon: '.$this->weapon->hit($target);
    }

    /**
     * Setter for weapon
     *
     * @param  net.xp_framework.unittest.ioc.stub.IWeapon $weapon
     * @return void
     */
    #[@inject]
    public function setWeapon(IWeapon $weapon) {
      $this->weapon= $weapon;
    }
  }
?>
