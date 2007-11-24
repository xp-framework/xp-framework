<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests destructor functionality
   *
   * @purpose  Testcase
   */
  class CloningTest extends TestCase {

    /**
     * Tests cloning of xp::null() which shouldn't work
     *
     */
    #[@test, @expect('lang.NullPointerException')]
    public function cloningOfNulls() {
      clone(xp::null());
    }

    /**
     * Tests cloning of an object without a __clone interceptor
     *
     */
    #[@test]
    public function cloneOfObject() {
      $original= new Object();
      $this->assertFalse($original == clone($original));
    }

    /**
     * Tests cloning of an object with a __clone interceptor
     *
     */
    #[@test]
    public function cloneInterceptorCalled() {
      $original= newinstance('lang.Object', array(), '{
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
      clone(newinstance('lang.Object', array(), '{
        function __clone() {
          throw(new CloneNotSupportedException("I am *UN*Cloneable"));
        }
      }'));
    }
  }
?>
