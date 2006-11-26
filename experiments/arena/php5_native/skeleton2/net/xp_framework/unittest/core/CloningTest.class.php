<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase', 
    'lang.CloneNotSupportedException'
  );

  /**
   * Tests destructor functionality
   *
   * @purpose  Testcase
   */
  class CloningTest extends TestCase {
    /**

     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public static function __static() {
      $cl= &ClassLoader::getDefault();
      $cl->defineClass('net.xp_framework.unittest.core.Cloneable', 'class Cloneable extends Object {
        var
          $cloned= FALSE;

        function __clone() {
          $this->cloned= TRUE;
        }
      }');
      $cl->defineClass('net.xp_framework.unittest.core.UnCloneable', 'class UnCloneable extends Object {
        function __clone() {
          throw(new CloneNotSupportedException("I am *UN*Cloneable"));
        }
      }');
    }

    /**
     * Tests cloning of xp::null() which shouldn't work
     *
     * @access  public
     */
    #[@test, @expect('lang.NullPointerException'), @ignore]
    public function cloningOfNulls() {
      clone(xp::null());
    }

    /**
     * Tests cloning of non-objects which shouldn't work
     *
     * @access  public
     */
    #[@test, @expect('lang.CloneNotSupportedException'), @ignore]
    public function cloningOfNonObjects() {
      clone(6100);
    }

    /**
     * Tests cloning of an object without a __clone interceptor
     *
     * @access  public
     */
    #[@test]
    public function cloneOfObject() {
      $original= new Object();
      $this->assertFalse($original == clone($original));
    }

    /**
     * Tests cloning of an object with a __clone interceptor
     *
     * @access  public
     */
    #[@test]
    public function cloneInterceptorCalled() {
      $original= new Cloneable();
      $this->assertFalse($original->cloned);
      $clone= clone $original;
      $this->assertFalse($original->cloned);
      $this->assertTrue($clone->cloned);
    }

    /**
     * Tests cloning of an object whose __clone interceptor throws a 
     * CloneNotSupportedException
     *
     * @access  public
     */
    #[@test, @expect('lang.CloneNotSupportedException')]
    public function cloneInterceptorThrowsException() {
      clone(new UnCloneable());
    }
  }
?>
