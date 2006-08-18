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
  class LoopEmitterTest extends AbstractEmitterTest {

    /**
     * Tests for-loop
     *
     * @access  public
     */
    #[@test]
    function forLoop() {
      $this->assertSourcecodeEquals(
        'for ($i= 0;$i<10;$i++) {echo $i; };',
        $this->emit('for ($i= 0; $i < 10; $i++) { echo $i; }')
      );
    }

    /**
     * Tests foreach-loop
     *
     * @access  public
     */
    #[@test]
    function foreachLoop() {
      $this->assertSourcecodeEquals(
        'foreach (range(1, 2, 3) as $i) {echo $i; };',
        $this->emit('foreach (range(1, 2, 3) as $i) { echo $i; }')
      );
    }

    /**
     * Tests foreach-loop
     *
     * @access  public
     */
    #[@test]
    function foreachLoopWithoutBraces() {
      $this->assertSourcecodeEquals(
        'foreach (range(1, 2, 3) as $i) {$j+= $i; };',
        $this->emit('foreach (range(1, 2, 3) as $i) $j+= $i;')
      );
    }

    /**
     * Tests while-loop
     *
     * @access  public
     */
    #[@test]
    function whileLoop() {
      $this->assertSourcecodeEquals(
        'while ($i<10) {echo $i++; };',
        $this->emit('while ($i < 10) { echo $i++; }')
      );
    }

    /**
     * Tests while-loop
     *
     * @access  public
     */
    #[@test]
    function whileLoopWithoutBraces() {
      $this->assertSourcecodeEquals(
        'while ($i<10) {$i++; };',
        $this->emit('while ($i < 10) $i++;')
      );
    }

    /**
     * Tests do-loop
     *
     * @access  public
     */
    #[@test]
    function doLoop() {
      $this->assertSourcecodeEquals(
        'do {echo $i++; } while ($i<10);',
        $this->emit('do { echo $i++; } while ($i < 10);')
      );
    }

    /**
     * Tests do-loop with a continue
     *
     * @access  public
     */
    #[@test]
    function doLoopWithContinue() {
      $this->assertSourcecodeEquals(
        'do {echo $i++; if ($i==5){ continue; }; } while ($i<10);',
        $this->emit('do { echo $i++; if ($i == 5) continue; } while ($i < 10);')
      );
    }

    /**
     * Tests do-loop with a break
     *
     * @access  public
     */
    #[@test]
    function doLoopWithBreak() {
      $this->assertSourcecodeEquals(
        'do {echo $i++; if ($i==5){ break; }; } while ($i<10);',
        $this->emit('do { echo $i++; if ($i == 5) break; } while ($i < 10);')
      );
    }
  }
?>
