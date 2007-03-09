<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class CastEmitterTest extends AbstractEmitterTest {

    /**
     * Tests a cast to int
     *
     */
    #[@test]
    public function integerCast() {
      $this->assertSourcecodeEquals(
        '$i= (int)$str;',
        $this->emit('$i= (int)$str;')
      );
    }

    /**
     * Tests a cast to string
     *
     */
    #[@test]
    public function stringCast() {
      $this->assertSourcecodeEquals(
        '$s= (string)$num;',
        $this->emit('$s= (string)$num;')
      );
    }

    /**
     * Tests a cast to bool
     *
     */
    #[@test]
    public function boolCast() {
      $this->assertSourcecodeEquals(
        '$s= (bool)$num;',
        $this->emit('$s= (bool)$num;')
      );
    }

    /**
     * Tests a cast to double
     *
     */
    #[@test]
    public function doubleCast() {
      $this->assertSourcecodeEquals(
        '$s= (double)$num;',
        $this->emit('$s= (double)$num;')
      );
    }

    /**
     * Tests a cast to array
     *
     */
    #[@test]
    public function arrayCast() {
      $this->assertSourcecodeEquals(
        '$s= (array)$num;',
        $this->emit('$s= (array)$num;')
      );
    }

    /**
     * Tests a cast to string
     *
     */
    #[@test]
    public function fqcnCast() {
      $this->assertSourcecodeEquals(
        '$s= xp::cast(\'lang.Object\', $o->getClass());',
        $this->emit('$s= (lang.Object)$o->getClass();')
      );
    }
  }
?>
