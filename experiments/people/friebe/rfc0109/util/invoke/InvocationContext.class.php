<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.invoke.Invocation', 'util.invoke.XPMain');

  /**
   * Represents the context a method was invoked from
   *
   * Example:
   * <code>
   *   class CallerTest extends Object {
   *     
   *     public function target() {
   *       Console::writeLine('I was called from: ', InvocationContext::getCaller()->toString());
   *     }
   *
   *     public function test() {
   *       $this->target();
   *     }
   *   }
   *
   *   $c= new CallerTest();
   *   $c->test();
   * </code>
   *
   * @test     xp://tests.InvocationContextTest
   * @purpose  Utility class
   */
  class InvocationContext extends Object {
    private static 
      $main= NULL;

    static function __static() {
      self::$main= new XPMain();
    }
  
    /**
     * Get the caller
     *
     * @param   int frame default 2
     * @return  util.invoke.Call
     */
    public static function getCaller($frame= 2) {
      $t= debug_backtrace();
      
      if (isset($t[$frame]['class'])) {
        $class= XPClass::forName(xp::nameOf($t[$frame]['class']));
      } else {
        $class= self::$main;
      }
      
      return new Invocation(
        $t[$frame]['object'],
        $class,
        isset($t[$frame]['function']) ? $t[$frame]['function']: 'main',
        $t[$frame]['args']
      );
    }
  }
?>
