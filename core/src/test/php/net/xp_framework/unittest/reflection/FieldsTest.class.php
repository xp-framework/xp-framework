<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.reflection.TestClass'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.reflect.Field
   * @purpose  Unittest
   */
  class FieldsTest extends TestCase {
    protected
      $fixture  = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= XPClass::forName('net.xp_framework.unittest.reflection.TestClass');
    }

    /**
     * Assertion helper
     *
     * @param   lang.Generic var
     * @param   lang.Generic[] list
     * @throws  unittest.AssertionFailedError
     */
    protected function assertNotContained($var, $list) {
      foreach ($list as $i => $element) {
        if ($element->equals($var)) $this->fail('Element contained', 'Found at offset '.$i, NULL);
      }
    }

    /**
     * Assertion helper
     *
     * @param   lang.Generic var
     * @param   lang.Generic[] list
     * @throws  unittest.AssertionFailedError
     */
    protected function assertContained($var, $list) {
      foreach ($list as $i => $element) {
        if ($element->equals($var)) return;
      }
      $this->fail('Element not contained in list', NULL, $var);
    }
    
    /**
     * Tests the field reflection
     *
     * @see     xp://lang.XPClass#getFields
     */
    #[@test]
    public function fields() {
      $fields= $this->fixture->getFields();
      $this->assertInstanceOf('lang.reflect.Field[]', $fields);
      $this->assertContained($this->fixture->getField('inherited'), $fields);
    }

    /**
     * Tests the field reflection
     *
     * @see     xp://lang.XPClass#getDeclaredFields
     */
    #[@test]
    public function declaredFields() {
      $fields= $this->fixture->getDeclaredFields();
      $this->assertInstanceOf('lang.reflect.Field[]', $fields);
      $this->assertNotContained($this->fixture->getField('inherited'), $fields);
    }

    /**
     * Tests field's declaring class
     *
     * @see     xp://lang.reflect.Field#getDeclaringClass
     */
    #[@test]
    public function declaredField() {
      $this->assertEquals(
        $this->fixture,
        $this->fixture->getField('map')->getDeclaringClass()
      );
    }

    /**
     * Tests field's declaring class
     *
     * @see     xp://lang.reflect.Field#getDeclaringClass
     */
    #[@test]
    public function inheritedField() {
      $this->assertEquals(
        $this->fixture->getParentClass(),
        $this->fixture->getField('inherited')->getDeclaringClass()
      );
    }

    /**
     * Tests checking for a non-existant field
     *
     * @see     xp://lang.XPClass#hasField
     */
    #[@test]
    public function nonExistantField() {
      $this->assertFalse($this->fixture->hasField('@@nonexistant@@'));
    }
    
    /**
     * Tests getting a non-existant field
     *
     * @see     xp://lang.XPClass#getField
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function getNonExistantField() {
      $this->fixture->getField('@@nonexistant@@');
    }

    /**
     * Tests the special "__id" member is not recognized as field
     *
     * @see     xp://lang.XPClass#hasField
     */
    #[@test]
    public function checkSpecialIdField() {
      $this->assertFalse($this->fixture->hasField('__id'));
    }
    
    /**
     * Tests the special "__id" member is not recognized as field
     *
     * @see     xp://lang.XPClass#getField
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function getSpecialIdField() {
      $this->fixture->getField('__id');
    }

    /**
     * Helper method
     *
     * @param   int modifiers
     * @param   string field
     * @throws  unittest.AssertionFailedError
     */
    protected function assertModifiers($modifiers, $field) {
      $this->assertEquals($modifiers, $this->fixture->getField($field)->getModifiers());
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function publicField() {
      $this->assertModifiers(MODIFIER_PUBLIC, 'date');
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function protectedField() {
      $this->assertModifiers(MODIFIER_PROTECTED, 'size');
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function privateField() {
      $this->assertModifiers(MODIFIER_PRIVATE, 'factor');
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function publicStaticField() {
      $this->assertModifiers(MODIFIER_PUBLIC | MODIFIER_STATIC, 'initializerCalled');
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function privateStaticField() {
      $this->assertModifiers(MODIFIER_PRIVATE | MODIFIER_STATIC, 'cache');
    }

    /**
     * Tests the field reflection for the "date" field
     *
     * @see     xp://lang.XPClass#getField
     * @see     xp://lang.XPClass#hasField
     */
    #[@test]
    public function dateField() {
      $this->assertTrue($this->fixture->hasField('date'));
      with ($field= $this->fixture->getField('date')); {
        $this->assertClass($field, 'lang.reflect.Field');
        $this->assertEquals('date', $field->getName());
        $this->assertEquals(XPClass::forName('util.Date'), $field->getType());
        $this->assertTrue($this->fixture->equals($field->getDeclaringClass()));
      }
    }

    /**
     * Tests retrieving the "date" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test]
    public function dateFieldValue() {
      $this->assertClass($this->fixture->getField('date')->get($this->fixture->newInstance()), 'util.Date');
    }

    /**
     * Tests reading and writing the "date" field
     *
     * @see     xp://lang.reflect.Field#get
     * @see     xp://lang.reflect.Field#set
     */
    #[@test]
    public function dateFieldRoundTrip() {
      $instance= $this->fixture->newInstance();
      $date= Date::now();
      $field= $this->fixture->getField('date');
      $field->set($instance, $date);
      $this->assertEquals($date, $field->get($instance));
    }

    /**
     * Tests retrieving the "date" field's value on a wrong object
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function getDateFieldValueOnWrongObject() {
      $this->fixture->getField('date')->get(new Object());
    }

    /**
     * Tests writing the "date" field's value on a wrong object
     *
     * @see     xp://lang.reflect.Field#set
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function setDateFieldValueOnWrongObject() {
      $this->fixture->getField('date')->set(new Object(), Date::now());
    }

    /**
     * Tests retrieving the "initializerCalled" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test]
    public function initializerCalledFieldValue() {
      $this->assertEquals(TRUE, $this->fixture->getField('initializerCalled')->get(NULL));
    }

    /**
     * Tests retrieving the "initializerCalled" field's value
     *
     * @see     xp://lang.reflect.Field#get
     * @see     xp://lang.reflect.Field#set
     */
    #[@test]
    public function initializerCalledFieldRoundTrip() {
      with ($f= $this->fixture->getField('initializerCalled')); {
        $f->set(NULL, FALSE);
        $this->assertEquals(FALSE, $f->get(NULL));
        $f->set(NULL, TRUE);
        $this->assertEquals(TRUE, $f->get(NULL));
      }
    }

    /**
     * Tests retrieving the private static "cache" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function getCacheFieldValue() {
      $this->fixture->getField('cache')->get(NULL);
    }

    /**
     * Tests setting the private static "cache" field's value
     *
     * @see     xp://lang.reflect.Field#set
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function setCacheFieldValue() {
      $this->fixture->getField('cache')->set(NULL, array());
    }

    /**
     * Tests retrieving the protected "size" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function getSizeFieldValue() {
      $this->fixture->getField('size')->get($this->fixture->newInstance());
    }

    /**
     * Tests setting the protected "size" field's value
     *
     * @see     xp://lang.reflect.Field#set
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function setSizeFieldValue() {
      $this->fixture->getField('size')->set($this->fixture->newInstance(), 1);
    }

    /**
     * Tests retrieving the private "factor" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function factorFieldValue() {
      $this->fixture->getField('factor')->get($this->fixture->newInstance());
    }

    /**
     * Tests retrieving the "date" field's string representation
     *
     * @see     xp://lang.reflect.Field#toString
     */
    #[@test]
    public function dateFieldString() {
      $this->assertEquals(
        'public util.Date net.xp_framework.unittest.reflection.TestClass::$date', 
        $this->fixture->getField('date')->toString()
      );
    }

    /**
     * Tests retrieving the "cache" field's string representation
     *
     * @see     xp://lang.reflect.Field#toString
     */
    #[@test]
    public function cacheFieldString() {
      $this->assertEquals(
        'private static var net.xp_framework.unittest.reflection.TestClass::$cache', 
        $this->fixture->getField('cache')->toString()
      );
    }

    /**
     * Tests retrieving the "date" field's is defined
     *
     * @see     xp://lang.reflect.Field#getType
     */
    #[@test]
    public function dateFieldType() {
      $this->assertEquals(XPClass::forName('util.Date'), $this->fixture->getField('date')->getType());
    }

    /**
     * Tests retrieving the "date" field's is defined
     *
     * @see     xp://lang.reflect.Field#getTypeName
     */
    #[@test]
    public function dateFieldTypeName() {
      $this->assertEquals('util.Date', $this->fixture->getField('date')->getTypeName());
    }

    /**
     * Tests retrieving the "cache" field's type is unknown
     *
     * @see     xp://lang.reflect.Field#getType
     */
    #[@test]
    public function cacheFieldType() {
      $this->assertEquals(Type::$VAR, $this->fixture->getField('cache')->getType());
    }

    /**
     * Tests retrieving the "cache" field's type is unknown
     *
     * @see     xp://lang.reflect.Field#getTypeName
     */
    #[@test]
    public function cacheFieldTypeName() {
      $this->assertEquals('var', $this->fixture->getField('cache')->getTypeName());
    }

    /**
     * Tests field details for inherited field
     *
     */
    #[@test]
    public function fieldTypeForInheritedField() {
      $this->assertEquals(XPClass::forName('lang.Object'), $this->fixture->getField('inherited')->getType());
    }

    /**
     * Tests field details for inherited field
     *
     */
    #[@test]
    public function fieldTypeNameForInheritedField() {
      $this->assertEquals('lang.Object', $this->fixture->getField('inherited')->getTypeName());
    }
  }
?>
