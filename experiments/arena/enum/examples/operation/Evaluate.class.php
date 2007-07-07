<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('examples.operation.Operation', 'util.cmd.Command');

  /**
   * Evaluates operations
   *
   * @purpose  Demo
   */
  class Evaluate extends Command {
    protected
      $x= 0, 
      $y= 0;
    
    /**
     * Set X
     *
     * @param   int x default 1
     */
    #[@arg(position= 0)]
    public function setX($x= 1) {
      $this->x= $x;
    }

    /**
     * Set Y
     *
     * @param   int Y default 1
     */
    #[@arg(position= 1)]
    public function setY($y= 1) {
      $this->y= $y;
    }
    
    /**
     * Run this command
     *
     */
    public function run() {
      foreach (Operation::values() as $op) {
        $this->out->writeLinef(
          '%d %s %s = %.1f', 
          $this->x, 
          $op->name, 
          $this->y, 
          $op->evaluate($this->x, $this->y)
        );
      }
    }
  }
?>
