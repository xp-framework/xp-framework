<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests arrays
   *
   */
  class net·xp_lang·tests·execution·source·ArrayTest extends ExecutionTest {
    
    /**
     * Test [1, 2]
     *
     */
    #[@test]
    public function untypedArray() {
      $this->assertEquals(array(1, 2), $this->run('return [1, 2];'));
    }

    /**
     * Test new string[] { "Hello", "World" }
     *
     */
    #[@test]
    public function typedArray() {
      $this->assertEquals(array('Hello', 'World'), $this->run('return new string[] { "Hello", "World" };'));
    }

    /**
     * Test [1, 2].length
     *
     */
    #[@test]
    public function arrayLength() {
      $this->assertEquals(2, $this->run('return [1, 2].length;'));
    }
  }
?>
