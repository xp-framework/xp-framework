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
    PENNY(1), NICKEL(5), DIME(10), QUARTER(25);
    
    /**
     * Returns color of a specified coin
     *
     * @model   static
     * @access  public
     * @param   int c
     * @return  string color
     * @throws  lang.IllegalArgumentException
     */
    function colorOf($c) {
      switch ($c) {
        case Coin_PENNY:    return 'COPPER';
        case Coin_NICKEL:   return 'NICKEL';
        case Coin_DIME:
        case Coin_QUARTER: return 'SILVER';
        default: throw(new IllegalArgumentException('Unknown coin '.$c));
      }
    }
  }
?>
