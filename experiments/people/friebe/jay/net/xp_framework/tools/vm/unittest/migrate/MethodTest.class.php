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
     * @access  public
     */
    #[@test]
    function defaultModifierAndReturnValue() {
      $this->assertMethodRewritten(
        'public void noop() { }', 
        'noop', array(), '() { }'
      );
    }

    /**
     * Tests protected modifier
     *
     * @access  public
     */
    #[@test]
    function protectedMethod() {
      $this->assertMethodRewritten(
        'protected void noop() { }', 
        'noop', array('@access' => array('protected')), '() { }'
      );
    }

    /**
     * Tests protected modifier
     *
     * @access  public
     */
    #[@test]
    function privateMethod() {
      $this->assertMethodRewritten(
        'private void noop() { }', 
        'noop', array('@access' => array('private')), '() { }'
      );
    }

    /**
     * Tests static modifier
     *
     * @access  public
     */
    #[@test]
    function staticMethod() {
      $this->assertMethodRewritten(
        'public static void noop() { }', 
        'noop', array('@model' => array('static')), '() { }'
      );
    }

    /**
     * Tests abstract modifier
     *
     * @access  public
     */
    #[@test]
    function abstractMethod() {
      $this->assertMethodRewritten(
        'public abstract void noop();', 
        'noop', array('@model' => array('abstract')), '() { }'
      );
    }

    /**
     * Tests abstract modifier
     *
     * @access  public
     */
    #[@test]
    function methodsInInterface() {
      $this->rewriter->names->current->type = INTERFACE_CLASS;
      $this->assertMethodRewritten(
        'public void noop();', 
        'noop', array(), '() { }'
      );
    }
  }
?>
