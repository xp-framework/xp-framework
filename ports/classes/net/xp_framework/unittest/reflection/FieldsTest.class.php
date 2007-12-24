<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase'
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
     * Tests the field reflection
     *
     * @see     xp://lang.XPClass#getFields
     */
    #[@test]
    public function fields() {
      $fields= $this->fixture->getFields();
      $this->assertArray($fields);
      foreach ($fields as $field) {
        $this->assertClass($field, 'lang.reflect.Field');
      }
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
     * Tests getting a non-existant field
     *
     * @see     xp://lang.reflect.Field#getField
     */
    #[@test]
    public function nonExistantField() {
      $this->assertFalse($this->fixture->hasField('@@nonexistant@@'));
      $this->assertNull($this->fixture->getField('@@nonexistant@@'));
    }

    /**
     * Tests the special "__id" member is not recognized as field
     *
     * @see     xp://lang.reflect.Field#getField
     */
    #[@test]
    public function specialIdField() {
      $this->assertFalse($this->fixture->hasField('__id'));
      $this->assertNull($this->fixture->getField('__id'));
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
        $this->assertEquals('util.Date', $field->getType());
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
     * Tests retrieving the "date" field's value on a wrong object
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function dateFieldValueOnWrongObject() {
      $this->fixture->getField('date')->get(new Object());
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
     * Tests retrieving the private static "cache" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function cacheFieldValue() {
      $this->fixture->getField('cache')->get(NULL);
    }

    /**
     * Tests retrieving the protected "size" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function sizeFieldValue() {
      $this->fixture->getField('size')->get($this->fixture->newInstance());
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
  }
?>
