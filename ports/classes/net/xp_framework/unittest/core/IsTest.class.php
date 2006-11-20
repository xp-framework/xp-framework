<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests the is() core functionality
   *
   * @see      php://is_a
   * @purpose  Testcase
   */
  class IsTest extends TestCase {

    /**
     * Tests the is() core function will recognize xp::null as null
     *
     * @access  public
     */
    #[@test]
    function xpNullIsNull() {
      $this->assertTrue(is(NULL, xp::null()));
    }

    /**
     * Ensures is() works with short class names
     *
     * @access  public
     */
    #[@test]
    function shortClassName() {
      $this->assertTrue(is('Object', new Object()));
    }

    /**
     * Ensures is() works with undefined class names
     *
     * @access  public
     */
    #[@test]
    function undefinedClassName() {
      $this->assertFalse(class_exists('Undefined_Class'));
      $this->assertFalse(is('Undefined_Class', new Object()));
    }

    /**
     * Ensures is() works with fully qualified class names
     *
     * @access  public
     */
    #[@test]
    function fullyQualifiedClassName() {
      $this->assertTrue(is('lang.Object', new Object()));
    }

    /**
     * Ensures is() works for interfaces
     *
     * @access  public
     */
    #[@test]
    function interfaces() {
      $cl= &ClassLoader::getDefault();
      $cl->defineClass(
        'DestructionCallbackImpl', 
        'class DestructionCallbackImpl extends Object {
          function onDestruction(&$object) { 
            // ... Implementation here
          }
        } implements("DestructionCallbackImpl.class.php", "net.xp_framework.unittest.core.DestructionCallback");'
      );
      $cl->defineClass(
        'DestructionCallbackImplEx', 
        'class DestructionCallbackImplEx extends DestructionCallbackImpl { }'
      );
      
      $this->assertTrue(is('DestructionCallback', new DestructionCallbackImpl()));
      $this->assertTrue(is('DestructionCallback', new DestructionCallbackImplEx()));
      $this->assertFalse(is('DestructionCallback', new Object()));
    }
  }
?>
