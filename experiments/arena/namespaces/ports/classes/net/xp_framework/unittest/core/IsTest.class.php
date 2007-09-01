<?php
/* This class is part of the XP framework
 *
 * $Id: IsTest.class.php 10292 2007-05-08 15:27:36Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.DestructionCallback'
  );

  /**
   * Tests the is() core functionality
   *
   * @see      php://is_a
   * @purpose  Testcase
   */
  class IsTest extends unittest::TestCase {

    /**
     * Tests the is() core function will recognize xp::null as null
     *
     */
    #[@test]
    public function xpNullIsNull() {
      $this->assertTrue(::is(NULL, ::xp::null()));
      $this->assertFalse(::is(NULL, 1));
    }

    /**
     * Ensures is() works with short class names
     *
     */
    #[@test]
    public function shortClassName() {
      $this->assertTrue(::is('Generic', new lang::Object()));
    }

    /**
     * Ensures is() works with undefined class names
     *
     */
    #[@test]
    public function undefinedClassName() {
      $this->assertFalse(class_exists('Undefined_Class'));
      $this->assertFalse(::is('Undefined_Class', new lang::Object()));
    }

    /**
     * Ensures is() works with fully qualified class names
     *
     */
    #[@test]
    public function fullyQualifiedClassName() {
      $this->assertTrue(::is('lang.Generic', new lang::Object()));
    }

    /**
     * Ensures is() works for interfaces
     *
     */
    #[@test]
    public function interfaces() {
      lang::ClassLoader::defineClass(
        'DestructionCallbackImpl', 
        'lang.Object',
        array('net.xp_framework.unittest.core.DestructionCallback'),
        '{
          function onDestruction($object) { 
            // ... Implementation here
          }
        }'
      );
      lang::ClassLoader::defineClass(
        'DestructionCallbackImplEx', 
        'DestructionCallbackImpl',
        NULL,
        '{}'
      );
      
      $this->assertTrue(::is('DestructionCallback', new DestructionCallbackImpl()));
      $this->assertTrue(::is('DestructionCallback', new DestructionCallbackImplEx()));
      $this->assertFalse(::is('DestructionCallback', new lang::Object()));
    }
  }
?>
