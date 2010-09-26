<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests chaining
   *
   */
  class net·xp_lang·tests·execution·source·ChainingTest extends ExecutionTest {
  
    /**
     * Test
     *
     */
    #[@test]
    public function parentOfTestClass() {
      $this->assertEquals(
        'lang.Object', 
        $this->run('return $this.getClass().getParentClass().getName();')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function firstMethodOfTestClass() {
      $this->assertEquals(
        'run', 
        $this->run('return $this.getClass().getMethods()[0].getName();')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function methodCallAfterNewObject() {
      $this->assertEquals(
        FALSE, 
        $this->run('return new Object().equals($this);')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function chainedMethodCallAfterNewObject() {
      $this->assertEquals(
        'lang.Object', 
        $this->run('return new Object().getClass().getName();')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function chainedNestedMethodCallAfterNewObject() {
      $this->assertEquals(
        new String('Test'), 
        $this->run('return new lang.types.String($this.member).concat("Test");')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayAccessAfterNew() {
      $this->assertEquals(
        6,
        $this->run('return new lang.types.ArrayList(5, 6, 7)[1];')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayAccessAfterStaticMethod() {
      $this->assertEquals(
        6,
        $this->run('return lang.types.ArrayList::newInstance([5, 6, 7])[1];')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayAccessAfterNewTypedArray() {
      $this->assertEquals(
        6,
        $this->run('return new int[]{5, 6, 7}[1];')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayAccessAfterNewUntypedArray() {
      $this->assertEquals(
        6,
        $this->run('return [5, 6, 7][1];')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function memberAfterNewTypedArray() {
      $this->assertEquals(
        1, 
        $this->run('return new string[]{"Hello"}.length;')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function memberAfterNewUntypedArray() {
      $this->assertEquals(
        1, 
        $this->run('return ["Hello"].length;')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayOfArrays() {
      $this->assertEquals(
        4, 
        $this->run('$a= [[1, 2], [3, 4]]; return $a[1][1];')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function staticMemberArrayAccess() {
      $this->assertFalse($this->run('return isset(xp::$registry["errors"][__FILE__]);'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function afterBracedExpression() {
      $this->assertEquals(3, $this->run('return (1 ? new lang.types.ArrayList(1, 2, 3) : null).length;'));
    }
  }
?>
