<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests lambdas
   *
   */
  class net·xp_lang·tests·execution·source·LambdaTest extends ExecutionTest {
    
    /**
     * Test 
     *
     */
    #[@test]
    public function apply() {
      $this->assertEquals(array(2, 4, 6), $this->run(
        'return apply([1, 2, 3], #{ $a -> $a * 2 });',
        array('import static net.xp_lang.tests.execution.source.Functions.apply;')
      ));
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function filter() {
      $this->assertEquals(array(2, 4, 6, 8, 10), $this->run(
        'return filter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], #{ $a -> !($a & 1) });',
        array('import static net.xp_lang.tests.execution.source.Functions.filter;')
      ));
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function applyWithCapturing() {
      $this->assertEquals(array(3, 6, 9), $this->run(
        '$mul= 3; return apply([1, 2, 3], #{ $a -> $a * $mul });',
        array('import static net.xp_lang.tests.execution.source.Functions.apply;')
      ));
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function execution() {
      $this->assertEquals(3, $this->run(
        'return #{ $a -> $a + 1 }.(2);'
      ));
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function executionViaVariable() {
      $this->assertEquals(3, $this->run(
        '$plusone= #{ $a -> $a + 1 }; return $plusone.(2);'
      ));
    }
  }
?>
