<?php
/* This class is part of the XP framework
 *
 * $Id: CloningTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses('unittest.TestCase');

  /**
   * Tests destructor functionality
   *
   * @purpose  Testcase
   */
  class CloningTest extends unittest::TestCase {

    /**
     * Tests cloning of xp::null() which shouldn't work
     *
     */
    #[@test, @expect('lang.NullPointerException')]
    public function cloningOfNulls() {
      clone(::xp::null());
    }

    /**
     * Tests cloning of non-objects which shouldn't work
     *
     */
    #[@test, @expect('lang.CloneNotSupportedException'), @ignore('E_NOTICE in PHP5')]
    public function cloningOfNonObjects() {
      clone(6100);
    }

    /**
     * Tests cloning of an object without a __clone interceptor
     *
     */
    #[@test]
    public function cloneOfObject() {
      $original= new lang::Object();
      $this->assertFalse($original == clone($original));
    }

    /**
     * Tests cloning of an object with a __clone interceptor
     *
     */
    #[@test]
    public function cloneInterceptorCalled() {
      $original= ::newinstance('lang.Object', array(), '{
        var $cloned= FALSE;

        function __clone() {
          $this->cloned= TRUE;
        }
      }');
      $this->assertFalse($original->cloned);
      $clone= clone($original);
      $this->assertFalse($original->cloned);
      $this->assertTrue($clone->cloned);
    }

    /**
     * Tests cloning of an object whose __clone interceptor throws a 
     * CloneNotSupportedException
     *
     */
    #[@test, @expect('lang.CloneNotSupportedException')]
    public function cloneInterceptorThrowsException() {
      clone(::newinstance('lang.Object', array(), '{
        function __clone() {
          throw(new lang::CloneNotSupportedException("I am *UN*Cloneable"));
        }
      }'));
    }
  }
?>
