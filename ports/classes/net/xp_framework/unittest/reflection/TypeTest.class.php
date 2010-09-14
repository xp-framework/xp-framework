<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Type'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.Type
   */
  class TypeTest extends TestCase {
  
    /**
     * Test string type
     *
     */
    #[@test]
    public function stringType() {
      $this->assertEquals(Primitive::$STRING, Type::forName('string'));
    }

    /**
     * Test int type
     *
     */
    #[@test]
    public function intType() {
      $this->assertEquals(Primitive::$INT, Type::forName('int'));
    }

    /**
     * Test double type
     *
     */
    #[@test]
    public function doubleType() {
      $this->assertEquals(Primitive::$DOUBLE, Type::forName('double'));
    }

    /**
     * Test bool type
     *
     */
    #[@test]
    public function boolType() {
      $this->assertEquals(Primitive::$BOOLEAN, Type::forName('bool'));
    }

    /**
     * Test void type
     *
     */
    #[@test]
    public function voidType() {
      $this->assertEquals(Type::$VOID, Type::forName('void'));
    }

    /**
     * Test var type
     *
     */
    #[@test]
    public function varType() {
      $this->assertEquals(Type::$VAR, Type::forName('var'));
    }

    /**
     * Test "string[]"
     *
     */
    #[@test]
    public function arrayOfString() {
      $this->assertEquals(ArrayType::forName('string[]'), Type::forName('string[]'));
    }

    /**
     * Test "array<string, string>"
     *
     */
    #[@test]
    public function mapOfStringToString() {
      $this->assertEquals(Primitive::$ARRAY, Type::forName('array<string, string>'));
    }

    /**
     * Test "array"
     *
     */
    #[@test]
    public function arrayKeyword() {
      $this->assertEquals(ArrayType::forName('var[]'), Type::forName('array'));
    }

    /**
     * Test string type mis-spelled as "char"
     *
     */
    #[@test]
    public function stringTypeVariant() {
      $this->assertEquals(Primitive::$STRING, Type::forName('char'));
    }

    /**
     * Test int type mis-spelled as "integer"
     *
     */
    #[@test]
    public function intTypeVariant() {
      $this->assertEquals(Primitive::$INT, Type::forName('integer'));
    }

    /**
     * Test double type mis-spelled as "float"
     *
     */
    #[@test]
    public function doubleTypeVariant() {
      $this->assertEquals(Primitive::$DOUBLE, Type::forName('float'));
    }

    /**
     * Test bool type mis-spelled as "boolean"
     *
     */
    #[@test]
    public function boolTypeVariant() {
      $this->assertEquals(Primitive::$BOOLEAN, Type::forName('boolean'));
    }

    /**
     * Test var type mis-spelled as "mixed"
     *
     */
    #[@test]
    public function varTypeMixedVariant() {
      $this->assertEquals(Type::$ANY, Type::forName('mixed'));
    }

    /**
     * Test var type mis-spelled as "*"
     *
     */
    #[@test]
    public function varTypeStarVariant() {
      $this->assertEquals(Type::$ANY, Type::forName('*'));
    }

    /**
     * Test lang.Object
     *
     */
    #[@test]
    public function objectType() {
      $this->assertEquals(XPClass::forName('lang.Object'), Type::forName('lang.Object'));
    }

    /**
     * Test lang.Object
     *
     */
    #[@test]
    public function genericObjectType() {
      $this->assertEquals(XPClass::forName('util.collections.HashTable'), Type::forName('util.collections.HashTable<String, Object>'));
    }

    /**
     * Test isInstance() method on Type::$VAR
     *
     */
    #[@test]
    public function thisIsInstanceOfVar() {
      $this->assertTrue(Type::$VAR->isInstance($this));
    }

    /**
     * Test isInstance() method on Type::$VAR
     *
     */
    #[@test]
    public function nullIsInstanceOfVar() {
      $this->assertTrue(Type::$VAR->isInstance(NULL));
    }

    /**
     * Test isInstance() method on Type::$VAR
     *
     */
    #[@test]
    public function stringIsInstanceOfVar() {
      $this->assertTrue(Type::$VAR->isInstance(''));
    }

    /**
     * Test isInstance() method on Type::$VAR
     *
     */
    #[@test]
    public function intIsInstanceOfVar() {
      $this->assertTrue(Type::$VAR->isInstance(0));
    }

    /**
     * Test isInstance() method on Type::$VOID
     *
     */
    #[@test]
    public function thisIsNotInstanceOfVoid() {
      $this->assertFalse(Type::$VOID->isInstance($this));
    }

    /**
     * Test isInstance() method on Type::$VOID
     *
     */
    #[@test]
    public function nullIsNotInstanceOfVoid() {
      $this->assertFalse(Type::$VOID->isInstance(NULL));
    }

    /**
     * Test isInstance() method on Type::$VOID
     *
     */
    #[@test]
    public function stringIsNotInstanceOfVoid() {
      $this->assertFalse(Type::$VOID->isInstance(''));
    }

    /**
     * Test isInstance() method on Type::$VOID
     *
     */
    #[@test]
    public function intIsNotInstanceOfVoid() {
      $this->assertFalse(Type::$VOID->isInstance(0));
    }
  }
?>
