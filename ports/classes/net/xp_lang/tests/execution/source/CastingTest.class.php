<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests casting
   *
   */
  class net·xp_lang·tests·execution·source·CastingTest extends ExecutionTest {
    
    /**
     * Test
     *
     */
    #[@test]
    public function integerToString() {
      $this->assertEquals('1', $this->run('return 1 as string;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function stringToInteger() {
      $this->assertEquals(1, $this->run('return "1" as int;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function integerToDouble() {
      $this->assertEquals(1.0, $this->run('return 1 as double;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function doubleToInteger() {
      $this->assertEquals(1, $this->run('return 1.0 as int;'));
    }
 
    /**
     * Test
     *
     */
    #[@test]
    public function oneAsBoolean() {
      $this->assertTrue($this->run('return 1 as bool;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function zeroAsBoolean() {
      $this->assertFalse($this->run('return 0 as bool;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function nullAsBoolean() {
      $this->assertFalse($this->run('return null as bool;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function emptyStringAsBoolean() {
      $this->assertFalse($this->run('return "" as bool;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function stringAsBoolean() {
      $this->assertTrue($this->run('return "a" as bool;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function numericOneStringAsBoolean() {
      $this->assertTrue($this->run('return "1" as bool;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function numericZeroStringAsBoolean() {
      $this->assertFalse($this->run('return "0" as bool;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function zeroAsIntArray() {
      $this->assertEquals(array(0), $this->run('return 0 as int[];'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function stringAsStringArray() {
      $this->assertEquals(array('Hello'), $this->run('return "Hello" as string[];'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function nullAsVarArray() {
      $this->assertEquals(array(), $this->run('return null as var[];'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function dateAsObject() {
      $this->run('return new util.Date() as lang.Object;');
    }

    /**
     * Test
     *
     */
    #[@test, @expect('lang.ClassCastException')]
    public function objectAsDate() {
      $this->run('return new lang.Object() as util.Date;');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function unverifiedThisAsDate() {
      $this->run('return $this as util.Date?;');
    }

    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function objectAsString() {
      $this->run('return new lang.Object() as string;');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function objectAsArray() {
      $this->run('return new lang.Object() as var[];');
    }
  }
?>
