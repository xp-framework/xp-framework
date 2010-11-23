<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.collections.Vector',
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
     * Tests the is() core function with NULL
     *
     */
    #[@test]
    public function intIsNotIsNull() {
      $this->assertFalse(is(NULL, 1));
    }

    /**
     * Tests the is() core function with []
     *
     */
    #[@test]
    public function stringArray() {
      $this->assertTrue(is('string[]', array('Hello')));
    }

    /**
     * Tests the is() core function with []
     *
     */
    #[@test]
    public function varArray() {
      $this->assertFalse(is('string[]', array('Hello', 1, TRUE)));
    }

    /**
     * Tests the is() core function with []
     *
     */
    #[@test]
    public function intArray() {
      $this->assertTrue(is('int[]', array(1, 2, 3)));
    }

    /**
     * Tests the is() core function with []
     *
     */
    #[@test]
    public function objectArray() {
      $this->assertTrue(is('lang.Object[]', array(new Object(), new Object(), new Object())));
    }

    /**
     * Tests the is() core function with []
     *
     */
    #[@test]
    public function objectArrayWithNull() {
      $this->assertFalse(is('lang.Object[]', array(new Object(), new Object(), NULL)));
    }

    /**
     * Tests the is() core function with [:]
     *
     */
    #[@test]
    public function stringMap() {
      $this->assertTrue(is('[:string]', array('greet' => 'Hello', 'whom' => 'World')));
    }

    /**
     * Tests the is() core function with [:]
     *
     */
    #[@test]
    public function intMap() {
      $this->assertTrue(is('[:int]', array('greet' => 1, 'whom' => 2)));
    }

    /**
     * Tests the is() core function with string
     *
     */
    #[@test]
    public function stringPrimitive() {
      $this->assertTrue(is('string', 'Hello'));
    }

    /**
     * Tests the is() core function with string
     *
     */
    #[@test]
    public function nullNotAStringPrimitive() {
      $this->assertFalse(is('string', NULL));
    }

    /**
     * Tests the is() core function with bool
     *
     */
    #[@test]
    public function boolPrimitive() {
      $this->assertTrue(is('bool', TRUE));
    }

    /**
     * Tests the is() core function with bool
     *
     */
    #[@test]
    public function nullNotABoolPrimitive() {
      $this->assertFalse(is('bool', NULL));
    }

    /**
     * Tests the is() core function with double
     *
     */
    #[@test]
    public function doublePrimitive() {
      $this->assertTrue(is('double', 0.0));
    }

    /**
     * Tests the is() core function with double
     *
     */
    #[@test]
    public function nullNotADoublePrimitive() {
      $this->assertFalse(is('double', NULL));
    }

    /**
     * Tests the is() core function with int
     *
     */
    #[@test]
    public function intPrimitive() {
      $this->assertTrue(is('int', 0));
    }

    /**
     * Tests the is() core function with int
     *
     */
    #[@test]
    public function nullNotAnIntPrimitive() {
      $this->assertFalse(is('int', NULL));
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
          public function onDestruction($object) { 
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

    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function aStringVectorIsIsItself() {
      $this->assertTrue(is('Vector<string>', create('new Vector<string>')));
    }

    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function aStringVectorIsIsItselfQualified() {
      $this->assertTrue(is('util.collections.Vector<string>', create('new Vector<string>')));
    }

    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function aVectorIsNotAStringVector() {
      $this->assertFalse(is('Vector<string>', new Vector()));
    }

    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function aStringVectorIsNotAVector() {
      $this->assertFalse(is('Vector', create('new Vector<string>')));
    }

    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function anIntVectorIsNotAStringVector() {
      $this->assertFalse(is('Vector<string>', create('new Vector<int>')));
    }

    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function aVectorOfIntVectorsIsItself() {
      $this->assertTrue(is('Vector<Vector<int>>', create('new Vector<Vector<int>>')));
    }

    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function aVectorOfIntVectorsIsNotAVectorOfStringVectors() {
      $this->assertFalse(is('Vector<Vector<string>>', create('new Vector<Vector<int>>')));
    }
 
    /**
     * Ensures is() works with generics
     *
     */
    #[@test]
    public function anIntVectorIsNotAnUndefinedGeneric() {
      $this->assertFalse(is('Undefined_Class<string>', create('new Vector<int>')));
    }
  }
?>
