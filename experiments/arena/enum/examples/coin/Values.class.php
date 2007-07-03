<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('examples.coin.Coin', 'util.cmd.Command');

  /**
   * Prints all coins' values
   *
   * @purpose  Demo
   */
  class Values extends Command {
  
    /**
     * Run this command
     *
     */
    public function run() {
      foreach (Coin::values() as $coin) {
        $this->out->writeLine($coin->name, ': ', $coin->value(), '¢ (', $coin->color(), ')');
      }
    }
  }
?>
