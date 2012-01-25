<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Represents a Weapon mock
   *
   */
  interface IWeapon {

    /**
     * Hits the specified target
     *
     * @param  string $target
     * @return string
     */
    public function hit($target);
  }
?>
