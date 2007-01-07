<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * Enumeration of US coins
   *
   * @purpose  Enumeration
   */
  enum Coin {
    penny(1), nickel(5), dime(10), quarter(25);
    
    /**
     * Returns color of this coin
     *
     * @return  string color
     */
    public function color() {
      switch ($this->ordinal) {
        case penny: return 'copper';
        case nickel: return 'nickel';
        case dime: case quarter: return 'silver';
      }
    }
  }
?>
