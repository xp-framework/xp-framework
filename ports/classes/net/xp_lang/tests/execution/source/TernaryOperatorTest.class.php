<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests ternary operator
   *
   */
  class net·xp_lang·tests·execution·source·TernaryOperatorTest extends ExecutionTest {
    
    /**
     * Test a ? a : b
     *
     */
    #[@test]
    public function simpleForm() {
      $this->assertNull($this->run('$i= 0; return $i ? $i : null;'));
    }

    /**
     * Test a ?: b
     *
     */
    #[@test]
    public function simpleFormOtherwayAround() {
      $this->assertEquals(1, $this->run('$i= 1; return $i ? $i : null;'));
    }

    /**
     * Test a ?: b
     *
     */
    #[@test]
    public function shortForm() {
      $this->assertNull($this->run('$i= 0; return $i ?: null;'));
    }
 
    /**
     * Test a ?: b
     *
     */
    #[@test]
    public function shortFormOtherwayAround() {
      $this->assertEquals(1, $this->run('$i= 1; return $i ?: null;'));
    }
  }
?>
