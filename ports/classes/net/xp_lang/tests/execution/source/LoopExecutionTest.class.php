<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests loop executions
   *
   */
  class net·xp_lang·tests·execution·source·LoopExecutionTest extends ExecutionTest {
    
    /**
     * Test foreach
     *
     */
    #[@test]
    public function foreachLoop() {
      $this->assertEquals(array(1, 2, 3), $this->run('
        $r= []; 
        foreach ($arg in [1, 2, 3]) { $r[]= $arg; } 
        return $r;
      '));
    }

    /**
     * Test foreach
     *
     */
    #[@test]
    public function emptyForeachLoop() {
      $this->assertNull($this->run('foreach ($arg in [1, 2, 3]) { }'));
    }

    /**
     * Test while
     *
     */
    #[@test]
    public function whileLoop() {
      $this->assertEquals(array(1, 2, 3), $this->run('
        $r= []; $s= 1;
        while ($s <= 3) { $r[]= $s++; } 
        return $r;
      '));
    }

    /**
     * Test while
     *
     */
    #[@test]
    public function emptyWhileLoop() {
      $this->assertEquals(-1, $this->run('$s= 3; while ($s--) { } return $s;'));
    }

    /**
     * Test do
     *
     */
    #[@test]
    public function doLoop() {
      $this->assertEquals(array(1, 2, 3), $this->run('
        $r= []; $s= 1;
        do { $r[]= $s++; } while ($s < 4);
        return $r;
      '));
    }

    /**
     * Test do
     *
     */
    #[@test]
    public function emptyDoLoop() {
      $this->assertEquals(-1, $this->run('$s= 3; do { } while ($s--); return $s;'));
    }

    /**
     * Test for
     *
     */
    #[@test]
    public function forLoop() {
      $this->assertEquals(array(1, 2, 3), $this->run('
        $r= [];
        for ($s= 1; $s <= 3; $s++) {
          $r[]= $s;
        }
        return $r;
      '));
    }

    /**
     * Test for
     *
     */
    #[@test]
    public function emptyForLoop() {
      $this->assertEquals(0, $this->run('for ($s= 3; $s; $s--) { } return $s;'));
    }

    /**
     * Test continue
     *
     */
    #[@test]
    public function continueInsideLoop() {
      $this->assertEquals(4, $this->run('foreach ($n in [1, 2, 3, 4]) {
        continue;
        throw new Error("Unreachable");
      } return $n;'));
    }

    /**
     * Test break
     *
     */
    #[@test]
    public function breakInsideLoop() {
      $this->assertEquals(1, $this->run('foreach ($n in [1, 2, 3, 4]) {
        break;
        throw new Error("Unreachable");
      } return $n;'));
    }
  }
?>
