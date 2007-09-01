<?php
/* This class is part of the XP framework
 *
 * $Id: NullTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses('unittest.TestCase');

  /**
   * Tests the "NULL-safe" xp::null. Requires the overload extension to
   * be enabled.
   *
   * @purpose  Testcase
   */
  class NullTest extends unittest::TestCase {

    /**
     * Setup method. Ensures overload extension is enabled.
     *
     */
    public function setUp() {
    }
    
    /**
     * Tests the is() core function will recognize xp::null as null
     *
     */
    #[@test]
    public function isNull() {
      $this->assertTrue(::is(NULL, ::xp::null()));
    }

    /**
     * Tests the is() core function will not recognize xp::null as an object
     *
     */
    #[@test]
    public function isNotAnObject() {
      $this->assertFalse(::is('lang.Generic', ::xp::null()));
    }

    /**
     * Tests xp::null evaluates to FALSE so it can be used in constructs
     * such as:
     * <code>
     *   function &constructorOf(&$class) {
     *     if (!$class->hasConstructor()) return xp::null();
     *     return $class->getConstructor();
     *   }
     *   
     *   if (!($c= &constructorOf($class))) { ... }
     * </code>
     *
     */
    #[@test, @ignore('Does no longer work with PHP5')]
    public function isFalse() {
      $this->assertTrue(!::xp::null());
    }

    /**
     * Tests the xp::typeOf() function's return value for xp::nulls
     *
     */
    #[@test]
    public function typeOf() {
      $this->assertEquals('<null>', ::xp::typeOf(::xp::null()));
    }
    
    /**
     * Tests the xp::stringOf() function's return value for xp::nulls
     *
     */
    #[@test]
    public function stringOf() {
      $this->assertEquals('<null>', ::xp::stringOf(::xp::null()));
    }
    
    /**
     * Tests creating new instances of xp::null will fail. The correct
     * way to retrieve an xp::null is to call xp:null()
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function newInstance() {
      new ::null();
    }

    /**
     * Tests cloning xp::null will result in a NPE
     *
     */
    #[@test, @expect('lang.NullPointerException')]
    public function cloneNull() {
      clone(::xp::null());
    }

    /**
     * Tests member invocation on xp::null will result in a NPE
     *
     */
    #[@test, @expect('lang.NullPointerException')]
    public function methodInvocation() {
      $null= ::xp::null();
      $null->method();
    }

    /**
     * Tests member read access on xp::null will result in a NPE
     *
     */
    #[@test, @expect('lang.NullPointerException')]
    public function memberReadAccess() {
      $null= ::xp::null();
      $i= $null->member;
    }
    
    /**
     * Tests member write access on xp::null will result in a NPE
     *
     */
    #[@test, @expect('lang.NullPointerException')]
    public function memberWriteccess() {
      $null= ::xp::null();
      $null->member= $i;
    }
  }
?>
