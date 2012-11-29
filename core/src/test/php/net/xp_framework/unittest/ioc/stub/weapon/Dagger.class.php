<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.ioc.stub.IWeapon');

  /**
   * Represents a Dagger stub
   *
   */
  class Dagger extends Object implements IWeapon {

    #[@inject(constant= 'dagger.damage')]
    protected $damage= 10;

    /**
     * {@inheritDoc}
     *
     */
    public function hit($target) {
      return 'Stab '.$target.' for '.$this->damage.' damage';
    }
  }
?>
