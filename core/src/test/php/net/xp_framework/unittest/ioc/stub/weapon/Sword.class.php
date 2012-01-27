<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.ioc.stub.IWeapon');

  /**
   * Represents a Sword stub
   *
   */
  class Sword extends Object implements IWeapon {
    protected $damage= 20;

    /**
     * {@inheritDoc}
     *
     */
    public function hit($target) {
      return 'Cut '.$target.' for '.$this->damage.' damage';
    }

    /**
     * Setter for damage
     *
     * @param  int $damage
     * @return void
     */
    #[@inject(constant= 'sword.damage')]
    public function setDamage($damage) {
      $this->damage= $damage;
    }
  }
?>
