<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Type',
    'util.collections.Vector',
    'util.collections.HashTable'
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
     * Test boolean type
     *
     */
    #[@test]
    public function boolType() {
      $this->assertEquals(Primitive::$BOOLEAN, Type::forName('boolean'));
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
     * Test "[:string]"
     *
     */
    #[@test]
    public function mapOfString() {
      $this->assertEquals(MapType::forName('[:string]'), Type::forName('[:string]'));
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
     * Test Object
     *
     */
    #[@test]
    public function objectTypeShortClass() {
      $this->assertEquals(XPClass::forName('lang.Object'), Type::forName('Object'));
    }

    /**
     * Test "Vector<string>"
     *
     */
    #[@test]
    public function generic() {
      $this->assertEquals(
        XPClass::forName('util.collections.Vector')->newGenericType(array(Primitive::$STRING)), 
        Type::forName('util.collections.Vector<string>')
      );
    }

    /**
     * Test "Vector<string>"
     *
     */
    #[@test]
    public function genericShortClass() {
      $this->assertEquals(
        XPClass::forName('util.collections.Vector')->newGenericType(array(Primitive::$STRING)), 
        Type::forName('Vector<string>')
      );
    }

    /**
     * Test "Vector<Vector<int>>"
     *
     */
    #[@test]
    public function genericOfGeneneric() {
      $vectorClass= XPClass::forName('util.collections.Vector');
      $this->assertEquals(
        $vectorClass->newGenericType(array($vectorClass->newGenericType(array(Primitive::$INT)))), 
        Type::forName('util.collections.Vector<util.collections.Vector<int>>')
      );
    }

    /**
     * Test "Vector<Vector<int>>"
     *
     */
    #[@test]
    public function genericOfGenenericShortClass() {
      $vectorClass= XPClass::forName('util.collections.Vector');
      $this->assertEquals(
        $vectorClass->newGenericType(array($vectorClass->newGenericType(array(Primitive::$INT)))), 
        Type::forName('Vector<Vector<int>>')
      );
    }

    /**
     * Test util.collections.HashTable<String, Object>
     *
     */
    #[@test]
    public function genericObjectType() {
      with ($t= Type::forName('util.collections.HashTable<String, Object>')); {
        $this->assertInstanceOf('lang.XPClass', $t);
        $this->assertTrue($t->isGeneric());
        $this->assertEquals(XPClass::forName('util.collections.HashTable'), $t->genericDefinition());
        $this->assertEquals(
          array(XPClass::forName('lang.types.String'), XPClass::forName('lang.Object')), 
          $t->genericArguments()
        );
      }
    }

    /**
     * Test "array"
     *
     * @deprecated
     */
    #[@test]
    public function arrayKeyword() {
      $this->assertEquals(ArrayType::forName('var[]'), Type::forName('array'));
    }

    /**
     * Test string type mis-spelled as "char"
     *
     * @deprecated
     */
    #[@test]
    public function stringTypeVariant() {
      $this->assertEquals(Primitive::$STRING, Type::forName('char'));
    }

    /**
     * Test int type mis-spelled as "integer"
     *
     * @deprecated
     */
    #[@test]
    public function intTypeVariant() {
      $this->assertEquals(Primitive::$INT, Type::forName('integer'));
    }

    /**
     * Test "array<string, string>" (deprecated syntax)
     *
     * @deprecated
     */
    #[@test]
    public function mapOfStringDeprecatedSyntax() {
      $this->assertEquals(MapType::forName('[:string]'), Type::forName('array<string, string>'));
    }

    /**
     * Test "array<string>" (deprecated syntax)
     *
     * @deprecated
     */
    #[@test]
    public function stringArrayDeprecatedSyntax() {
      $this->assertEquals(ArrayType::forName('string[]'), Type::forName('array<string>'));
    }

    /**
     * Test double type mis-spelled as "float"
     *
     * @deprecated
     */
    #[@test]
    public function doubleTypeVariant() {
      $this->assertEquals(Primitive::$DOUBLE, Type::forName('float'));
    }

    /**
     * Test bool type mis-spelled as "boolean"
     *
     * @deprecated
     */
    #[@test]
    public function booleanTypeVariant() {
      $this->assertEquals(Primitive::$BOOLEAN, Type::forName('bool'));
    }

    /**
     * Test var type mis-spelled as "mixed"
     *
     * @deprecated
     */
    #[@test]
    public function varTypeMixedVariant() {
      $this->assertEquals(Type::$VAR, Type::forName('mixed'));
    }

    /**
     * Test var type mis-spelled as "*"
     *
     * @deprecated
     */
    #[@test]
    public function varTypeStarVariant() {
      $this->assertEquals(Type::$VAR, Type::forName('*'));
    }

    /**
     * Test "resource" as type name
     *
     */
    #[@test]
    public function resourceType() {
      $this->assertEquals(Type::$VAR, Type::forName('resource'));
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
