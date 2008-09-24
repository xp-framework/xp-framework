<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.DestructionCallback'
  );

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
     */
    #[@test]
    public function xpNullIsNull() {
      $this->assertTrue(is(NULL, xp::null()));
      $this->assertFalse(is(NULL, 1));
    }

    /**
     * Ensures is() works with short class names
     *
     */
    #[@test]
    public function shortClassName() {
      $this->assertTrue(is('Generic', new Object()));
    }

    /**
     * Ensures is() works with undefined class names
     *
     */
    #[@test]
    public function undefinedClassName() {
      $this->assertFalse(class_exists('Undefined_Class', FALSE));
      $this->assertFalse(is('Undefined_Class', new Object()));
    }

    /**
     * Ensures is() works with fully qualified class names
     *
     */
    #[@test]
    public function fullyQualifiedClassName() {
      $this->assertTrue(is('lang.Generic', new Object()));
    }

    /**
     * Ensures is() works for interfaces
     *
     */
    #[@test]
    public function interfaces() {
      ClassLoader::defineClass(
        'net.xp_framework.unittest.core.DestructionCallbackImpl', 
        'lang.Object',
        array('net.xp_framework.unittest.core.DestructionCallback'),
        '{
          function onDestruction($object) { 
            // ... Implementation here
          }
        }'
      );
      ClassLoader::defineClass(
        'net.xp_framework.unittest.core.DestructionCallbackImplEx', 
        'net.xp_framework.unittest.core.DestructionCallbackImpl',
        NULL,
        '{}'
      );
      
      $this->assertTrue(is('net.xp_framework.unittest.core.DestructionCallback', new DestructionCallbackImpl()));
      $this->assertTrue(is('net.xp_framework.unittest.core.DestructionCallback', new DestructionCallbackImplEx()));
      $this->assertFalse(is('net.xp_framework.unittest.core.DestructionCallback', new Object()));
    }
  }
?>
