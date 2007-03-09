<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.migrate.AbstractRewriterTest');

  /**
   * Tests method rewriting
   *
   * @purpose  Unit Test
   */
  class MethodTest extends AbstractRewriterTest {

    /**
     * Tests default modifier is "public" and default return type is "void".
     *
     */
    #[@test]
    public function defaultModifierAndReturnValue() {
      $this->assertMethodRewritten(
        'public void noop() { }', 
        'public', 'noop', array(), '() { }'
      );
    }

    /**
     * Tests protected modifier
     *
     */
    #[@test]
    public function protectedMethod() {
      $this->assertMethodRewritten(
        'protected void noop() { }', 
        'protected', 'noop', array(), '() { }'
      );
    }

    /**
     * Tests protected modifier
     *
     */
    #[@test]
    public function privateMethod() {
      $this->assertMethodRewritten(
        'private void noop() { }', 
        'private', 'noop', array(), '() { }'
      );
    }

    /**
     * Tests static modifier
     *
     */
    #[@test]
    public function staticMethod() {
      $this->assertMethodRewritten(
        'public static void noop() { }', 
        'public static', 'noop', array(), '() { }'
      );
    }

    /**
     * Tests final modifier
     *
     */
    #[@test]
    public function finalMethod() {
      $this->assertMethodRewritten(
        'public final void noop() { }', 
        'public final', 'noop', array(), '() { }'
      );
    }

    /**
     * Tests final and static modifier used together
     *
     */
    #[@test]
    public function finalStaticMethod() {
      $this->assertMethodRewritten(
        'public final static void noop() { }', 
        'public final static', 'noop', array(), '() { }'
      );
    }

    /**
     * Tests abstract modifier
     *
     */
    #[@test]
    public function abstractMethod() {
      $this->assertMethodRewritten(
        'public abstract void noop();', 
        'public abstract', 'noop', array(), '();'
      );
    }

    /**
     * Tests abstract modifier
     *
     */
    #[@test]
    public function methodsInInterface() {
      $this->assertMethodRewritten(
        'public void noop();', 
        'public', 'noop', array(), '();'
      );
    }
  }
?>
