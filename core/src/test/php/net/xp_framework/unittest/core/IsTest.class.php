<?php namespace net\xp_framework\unittest\core;

use util\collections\Vector;

/**
 * Tests the is() core functionality
 *
 * @see      php://is_a
 */
class IsTest extends \unittest\TestCase {

  #[@test]
  public function xpNullIsNull() {
    $this->assertTrue(is(NULL, \xp::null()));
    $this->assertFalse(is(NULL, 1));
  }

  #[@test]
  public function intIsNotIsNull() {
    $this->assertFalse(is(NULL, 1));
  }

  #[@test]
  public function stringArray() {
    $this->assertTrue(is('string[]', array('Hello')));
  }

  #[@test]
  public function varArray() {
    $this->assertFalse(is('string[]', array('Hello', 1, TRUE)));
  }

  #[@test]
  public function intArray() {
    $this->assertTrue(is('int[]', array(1, 2, 3)));
  }

  #[@test]
  public function mapIsNotAnIntArray() {
    $this->assertFalse(is('int[]', array('one' => 1, 'two' => 2)));
  }

  #[@test]
  public function intIsNotAnIntArray() {
    $this->assertFalse(is('int[]', 1));
  }

  #[@test]
  public function thisIsNotAnIntArray() {
    $this->assertFalse(is('int[]', $this));
  }

  #[@test]
  public function emptyArrayIsAnIntArray() {
    $this->assertTrue(is('int[]', array()));
  }

  #[@test]
  public function objectArray() {
    $this->assertTrue(is('lang.Object[]', array(new \lang\Object(), new \lang\Object(), new \lang\Object())));
  }

  #[@test]
  public function objectArrayWithNull() {
    $this->assertFalse(is('lang.Object[]', array(new \lang\Object(), new \lang\Object(), NULL)));
  }

  #[@test]
  public function stringMap() {
    $this->assertTrue(is('[:string]', array('greet' => 'Hello', 'whom' => 'World')));
  }

  #[@test]
  public function intMap() {
    $this->assertTrue(is('[:int]', array('greet' => 1, 'whom' => 2)));
  }

  #[@test]
  public function intArrayIsNotAnIntMap() {
    $this->assertFalse(is('[:int]', array(1, 2)));
  }

  #[@test]
  public function intIsNotAnIntMap() {
    $this->assertFalse(is('[:int]', 1));
  }

  #[@test]
  public function thisIsNotAnIntMap() {
    $this->assertFalse(is('[:int]', $this));
  }

  #[@test]
  public function emptyArrayIsAnIntMap() {
    $this->assertTrue(is('[:int]', array()));
  }

  #[@test]
  public function stringPrimitive() {
    $this->assertTrue(is('string', 'Hello'));
  }

  #[@test]
  public function nullNotAStringPrimitive() {
    $this->assertFalse(is('string', NULL));
  }

  #[@test]
  public function boolPrimitive() {
    $this->assertTrue(is('bool', TRUE));
  }

  #[@test]
  public function nullNotABoolPrimitive() {
    $this->assertFalse(is('bool', NULL));
  }

  #[@test]
  public function doublePrimitive() {
    $this->assertTrue(is('double', 0.0));
  }

  #[@test]
  public function nullNotADoublePrimitive() {
    $this->assertFalse(is('double', NULL));
  }

  #[@test]
  public function intPrimitive() {
    $this->assertTrue(is('int', 0));
  }

  #[@test]
  public function nullNotAnIntPrimitive() {
    $this->assertFalse(is('int', NULL));
  }

  #[@test]
  public function shortClassName() {
    $this->assertTrue(is('Generic', new \lang\Object()));
  }

  #[@test]
  public function undefinedClassName() {
    $this->assertFalse(class_exists('Undefined_Class', FALSE));
    $this->assertFalse(is('Undefined_Class', new \lang\Object()));
  }

  #[@test]
  public function fullyQualifiedClassName() {
    $this->assertTrue(is('lang.Generic', new \lang\Object()));
  }

  #[@test]
  public function interfaces() {
    \lang\ClassLoader::defineClass(
      'net.xp_framework.unittest.core.DestructionCallbackImpl', 
      'lang.Object',
      array('net.xp_framework.unittest.core.DestructionCallback'),
      '{
      public function onDestruction($object) { 
          // ... Implementation here
        }
      }'
    );
    \lang\ClassLoader::defineClass(
      'net.xp_framework.unittest.core.DestructionCallbackImplEx', 
      'net.xp_framework.unittest.core.DestructionCallbackImpl',
      NULL,
      '{}'
    );
    
    $this->assertTrue(is('net.xp_framework.unittest.core.DestructionCallback', new DestructionCallbackImpl()));
    $this->assertTrue(is('net.xp_framework.unittest.core.DestructionCallback', new DestructionCallbackImplEx()));
    $this->assertFalse(is('net.xp_framework.unittest.core.DestructionCallback', new \lang\Object()));
  }

  #[@test]
  public function aStringVectorIsIsItself() {
    $this->assertTrue(is('Vector<string>', create('new Vector<string>')));
  }

  #[@test]
  public function aStringVectorIsIsItselfQualified() {
    $this->assertTrue(is('util.collections.Vector<string>', create('new Vector<string>')));
  }

  #[@test]
  public function aVectorIsNotAStringVector() {
    $this->assertFalse(is('Vector<string>', new Vector()));
  }

  #[@test]
  public function aStringVectorIsNotAVector() {
    $this->assertFalse(is('Vector', create('new Vector<string>')));
  }

  #[@test]
  public function anIntVectorIsNotAStringVector() {
    $this->assertFalse(is('Vector<string>', create('new Vector<int>')));
  }

  #[@test]
  public function aVectorOfIntVectorsIsItself() {
    $this->assertTrue(is('Vector<Vector<int>>', create('new Vector<Vector<int>>')));
  }

  #[@test]
  public function aVectorOfIntVectorsIsNotAVectorOfStringVectors() {
    $this->assertFalse(is('Vector<Vector<string>>', create('new Vector<Vector<int>>')));
  }
 
  #[@test]
  public function anIntVectorIsNotAnUndefinedGeneric() {
    $this->assertFalse(is('Undefined_Class<string>', create('new Vector<int>')));
  }
}
