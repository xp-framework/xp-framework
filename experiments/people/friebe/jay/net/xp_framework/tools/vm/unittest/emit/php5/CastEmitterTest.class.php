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
     * @access  public
     */
    #[@test]
    function integerCast() {
      $this->assertSourcecodeEquals(
        '$i= (int)$str;',
        $this->emit('$i= (int)$str;')
      );
    }

    /**
     * Tests a cast to string
     *
     * @access  public
     */
    #[@test]
    function stringCast() {
      $this->assertSourcecodeEquals(
        '$s= (string)$num;',
        $this->emit('$s= (string)$num;')
      );
    }

    /**
     * Tests a cast to string
     *
     * @access  public
     */
    #[@test]
    function fqcnCast() {
      $this->assertSourcecodeEquals(
        '$s= xp::cast(\'lang.Object\', $o->getClass());',
        $this->emit('$s= (lang.Object)$o->getClass();')
      );
    }
  }
?>
