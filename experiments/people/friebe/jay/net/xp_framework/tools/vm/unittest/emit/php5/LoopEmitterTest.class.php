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
     */
    #[@test]
    public function forLoop() {
      $this->assertSourcecodeEquals(
        'for ($i= 0;$i<10;$i++) {echo $i; };',
        $this->emit('for ($i= 0; $i < 10; $i++) { echo $i; }')
      );
    }

    /**
     * Tests for-loop with missing loop statement
     *
     */
    #[@test]
    public function forLoopWithMissingLoop() {
      $this->assertSourcecodeEquals(
        'for ($i= 0;$i<10;) {$i++; };',
        $this->emit('for ($i= 0; $i < 10; ) {  $i++; }')
      );
    }

    /**
     * Tests for-loop with multiple init-statements
     *
     */
    #[@test]
    public function forLoopWithMultipleInits() {
      $this->assertSourcecodeEquals(
        'for ($i= 0, $s= sizeof($list);$i<$s;$i++) {echo $i; };',
        $this->emit('for ($i= 0, $s= sizeof($list); $i < $s; $i++) { echo $i; }')
      );
    }

    /**
     * Tests foreach-loop
     *
     */
    #[@test]
    public function foreachLoop() {
      $this->assertSourcecodeEquals(
        'foreach (range(1, 2, 3) as $i) {echo $i; };',
        $this->emit('foreach (range(1, 2, 3) as $i) { echo $i; }')
      );
    }

    /**
     * Tests foreach-loop
     *
     */
    #[@test]
    public function foreachLoopWithoutBraces() {
      $this->assertSourcecodeEquals(
        'foreach (range(1, 2, 3) as $i) {$j+= $i; };',
        $this->emit('foreach (range(1, 2, 3) as $i) $j+= $i;')
      );
    }

    /**
     * Tests while-loop
     *
     */
    #[@test]
    public function whileLoop() {
      $this->assertSourcecodeEquals(
        'while ($i<10) {echo $i++; };',
        $this->emit('while ($i < 10) { echo $i++; }')
      );
    }

    /**
     * Tests while-loop
     *
     */
    #[@test]
    public function whileLoopWithoutBraces() {
      $this->assertSourcecodeEquals(
        'while ($i<10) {$i++; };',
        $this->emit('while ($i < 10) $i++;')
      );
    }

    /**
     * Tests do-loop
     *
     */
    #[@test]
    public function doLoop() {
      $this->assertSourcecodeEquals(
        'do {echo $i++; } while ($i<10);',
        $this->emit('do { echo $i++; } while ($i < 10);')
      );
    }

    /**
     * Tests do-loop with a continue
     *
     */
    #[@test]
    public function doLoopWithContinue() {
      $this->assertSourcecodeEquals(
        'do {echo $i++; if ($i==5){ continue; }; } while ($i<10);',
        $this->emit('do { echo $i++; if ($i == 5) continue; } while ($i < 10);')
      );
    }

    /**
     * Tests do-loop with a break
     *
     */
    #[@test]
    public function doLoopWithBreak() {
      $this->assertSourcecodeEquals(
        'do {echo $i++; if ($i==5){ break; }; } while ($i<10);',
        $this->emit('do { echo $i++; if ($i == 5) break; } while ($i < 10);')
      );
    }
  }
?>
