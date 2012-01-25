<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.ioc.stub.IWeapon'
  );

  /**
   * Represents a Bow weapon
   *
   */
  class Bow extends Object implements IWeapon {

    #[@inject(constant= 'bow.damage')]
    protected $damage= 30;

    /**
     * {@inheritDoc}
     *
     */
    public function hit($target) {
      return 'Shot '.$target.' for '.$this->damage.' damage';
    }
  }
?>
