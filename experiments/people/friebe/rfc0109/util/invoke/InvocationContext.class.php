<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.invoke.Invocation');

  /**
   * (Insert class' description here)
   *
   * @test     xp://net.xp_framework.unittest.util.invoke.InvocationContextTest
   * @see      reference
   * @purpose  purpose
   */
  class InvocationContext extends Object {
  
    /**
     * (Insert method's description here)
     *
     * @param   int frame default 2
     * @return  util.invoke.Call
     */
    public static function getCaller($frame= 2) {
      $t= debug_backtrace();
      return new Invocation(
        $t[$frame]['object'],
        $t[$frame]['class'],
        $t[$frame]['function'],
        $t[$frame]['args']
      );
    }
  }
?>
