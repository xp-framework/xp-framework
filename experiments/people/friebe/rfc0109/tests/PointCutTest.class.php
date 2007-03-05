<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.invoke.PointCutExpression',
    'util.invoke.InvocationContext'
  );

  /**
   * TestCase
   *
   * @purpose  TestCase
   */
  class PointCutTest extends TestCase {

    /**
     * Test "call:tests.PointCutTest::callJoinPoint()" matches this test 
     * method's invocation
     *
     */
    #[@test]
    public function callJoinPoint() {
      $expr= new PointCutExpression('call:tests.PointCutTest::callJoinPoint()');
      $this->assertEquals(PointCutExpression::CALL, $expr->getJoinpoint());
      $this->assertTrue($expr->matches(InvocationContext::getCaller(1)));
    }

    /**
     * Test "call:tests.PointCutTest::setUp()" does not match this test 
     * method's invocation
     *
     */
    #[@test]
    public function unmatchedCallJoinPoint() {
      $expr= new PointCutExpression('call:tests.PointCutTest::setUp()');
      $this->assertEquals(PointCutExpression::CALL, $expr->getJoinpoint());
      $this->assertFalse($expr->matches(InvocationContext::getCaller(1)));
    }

    /**
     * Test "call:tests.PointCutTest::callJoinPoint(*)" matches this test 
     * method's invocation
     *
     */
    #[@test]
    public function callJoinPointWithStarArgs() {
      $expr= new PointCutExpression('call:tests.PointCutTest::callJoinPointWithStarArgs(*)');
      $this->assertEquals(PointCutExpression::CALL, $expr->getJoinpoint());
      $this->assertTrue($expr->matches(InvocationContext::getCaller(1)));
    }

    /**
     * Test "call:tests.PointCutTest::*()" matches this test method's 
     * invocation
     *
     */
    #[@test]
    public function callJoinPointWithStarMethod() {
      $expr= new PointCutExpression('call:tests.PointCutTest::*()');
      $this->assertEquals(PointCutExpression::CALL, $expr->getJoinpoint());
      $this->assertTrue($expr->matches(InvocationContext::getCaller(1)));
    }

    /**
     * Test "call:tests.PointCutTest::*(*)" matches this test method's 
     * invocation
     *
     */
    #[@test]
    public function callJoinPointWithStarMethodAndStarArgs() {
      $expr= new PointCutExpression('call:tests.PointCutTest::*(*)');
      $this->assertEquals(PointCutExpression::CALL, $expr->getJoinpoint());
      $this->assertTrue($expr->matches(InvocationContext::getCaller(1)));
    }

    /**
     * Tests illegal join point specification
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalJoinPoint() {
      new PointCutExpression('FOO:...');
    }

    /**
     * Tests 
     *
     */
    #[@test]
    public function constructJoinPoint() {
      $expr= new PointCutExpression('new:lang.Object(*)');
      $this->assertEquals(PointCutExpression::CONSTRUCT, $expr->getJoinpoint());
      
      $class= XPClass::forName('lang.Object');
      $this->assertTrue($expr->matches(new Invocation($this, $class, '__construct', array())));
    }
  }
?>
