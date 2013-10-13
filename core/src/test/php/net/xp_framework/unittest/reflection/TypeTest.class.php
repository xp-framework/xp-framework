<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;
use lang\Type;
use util\collections\Vector;
use util\collections\HashTable;


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
    $this->assertEquals(\lang\Primitive::$STRING, Type::forName('string'));
  }

  /**
   * Test int type
   *
   */
  #[@test]
  public function intType() {
    $this->assertEquals(\lang\Primitive::$INT, Type::forName('int'));
  }

  /**
   * Test double type
   *
   */
  #[@test]
  public function doubleType() {
    $this->assertEquals(\lang\Primitive::$DOUBLE, Type::forName('double'));
  }

  /**
   * Test boolean type
   *
   */
  #[@test]
  public function boolType() {
    $this->assertEquals(\lang\Primitive::$BOOLEAN, Type::forName('boolean'));
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
    $this->assertEquals(\lang\ArrayType::forName('string[]'), Type::forName('string[]'));
  }

  /**
   * Test "[:string]"
   *
   */
  #[@test]
  public function mapOfString() {
    $this->assertEquals(\lang\MapType::forName('[:string]'), Type::forName('[:string]'));
  }

  /**
   * Test lang.Object
   *
   */
  #[@test]
  public function objectType() {
    $this->assertEquals(\lang\XPClass::forName('lang.Object'), Type::forName('lang.Object'));
  }

  /**
   * Test Object
   *
   */
  #[@test]
  public function objectTypeShortClass() {
    $this->assertEquals(\lang\XPClass::forName('lang.Object'), Type::forName('Object'));
  }

  /**
   * Test "Vector<string>"
   *
   */
  #[@test]
  public function generic() {
    $this->assertEquals(
      \lang\XPClass::forName('util.collections.Vector')->newGenericType(array(\lang\Primitive::$STRING)), 
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
      \lang\XPClass::forName('util.collections.Vector')->newGenericType(array(\lang\Primitive::$STRING)), 
      Type::forName('Vector<string>')
    );
  }

  /**
   * Test "Vector<Vector<int>>"
   *
   */
  #[@test]
  public function genericOfGeneneric() {
    $vectorClass= \lang\XPClass::forName('util.collections.Vector');
    $this->assertEquals(
      $vectorClass->newGenericType(array($vectorClass->newGenericType(array(\lang\Primitive::$INT)))), 
      Type::forName('util.collections.Vector<util.collections.Vector<int>>')
    );
  }

  /**
   * Test "Vector<Vector<int>>"
   *
   */
  #[@test]
  public function genericOfGenenericShortClass() {
    $vectorClass= \lang\XPClass::forName('util.collections.Vector');
    $this->assertEquals(
      $vectorClass->newGenericType(array($vectorClass->newGenericType(array(\lang\Primitive::$INT)))), 
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
      $this->assertEquals(\lang\XPClass::forName('util.collections.HashTable'), $t->genericDefinition());
      $this->assertEquals(
        array(\lang\XPClass::forName('lang.types.String'), \lang\XPClass::forName('lang.Object')), 
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
    $this->assertEquals(\lang\ArrayType::forName('var[]'), Type::forName('array'));
  }

  /**
   * Test string type mis-spelled as "char"
   *
   * @deprecated
   */
  #[@test]
  public function stringTypeVariant() {
    $this->assertEquals(\lang\Primitive::$STRING, Type::forName('char'));
  }

  /**
   * Test int type mis-spelled as "integer"
   *
   * @deprecated
   */
  #[@test]
  public function intTypeVariant() {
    $this->assertEquals(\lang\Primitive::$INT, Type::forName('integer'));
  }

  /**
   * Test "array<string, string>" (deprecated syntax)
   *
   * @deprecated
   */
  #[@test]
  public function mapOfStringDeprecatedSyntax() {
    $this->assertEquals(\lang\MapType::forName('[:string]'), Type::forName('array<string, string>'));
  }

  /**
   * Test "array<string>" (deprecated syntax)
   *
   * @deprecated
   */
  #[@test]
  public function stringArrayDeprecatedSyntax() {
    $this->assertEquals(\lang\ArrayType::forName('string[]'), Type::forName('array<string>'));
  }

  /**
   * Test double type mis-spelled as "float"
   *
   * @deprecated
   */
  #[@test]
  public function doubleTypeVariant() {
    $this->assertEquals(\lang\Primitive::$DOUBLE, Type::forName('float'));
  }

  /**
   * Test bool type mis-spelled as "boolean"
   *
   * @deprecated
   */
  #[@test]
  public function booleanTypeVariant() {
    $this->assertEquals(\lang\Primitive::$BOOLEAN, Type::forName('bool'));
  }

  /**
   * Test var type mis-spelled as "mixed" or "*"
   *
   * @deprecated
   */
  #[@test, @values('mixed', '*')]
  public function varTypeVariant($name) {
    $this->assertEquals(Type::$VAR, Type::forName($name));
  }

  /**
   * Test "resource" as type name
   */
  #[@test]
  public function resourceType() {
    $this->assertEquals(Type::$VAR, Type::forName('resource'));
  }

  /**
   * Returns instances of all types
   *
   * @return  var[]
   */
  public function instances() {
    return array($this, null, false, true, '', 0, 0.0, array(array()), array('one' => 'two'));
  }

  /**
   * Test isInstance() method on Type::$VAR
   */
  #[@test, @values('instances')]
  public function anythingIsAnInstanceOfVar($value) {
    $this->assertTrue(Type::$VAR->isInstance($value));
  }

  /**
   * Test isInstance() method on Type::$VOID
   */
  #[@test, @values('instances')]
  public function nothingIsAnInstanceOfVoid($value) {
    $this->assertFalse(Type::$VOID->isInstance($value));
  }

  /**
   * Returns all types
   *
   * @return  var[]
   */
  public function types() {
    return array(
      $this->getClass(),
      Type::$VAR, Type::$VOID,
      \lang\Primitive::$BOOLEAN, \lang\Primitive::$STRING, \lang\Primitive::$INT, \lang\Primitive::$DOUBLE,
      \lang\ArrayType::forName('var[]'),
      \lang\MapType::forName('[:var]')
    );
  }

  /**
   * Test isInstance() method on Type::$VAR
   */
  #[@test, @values('instances')]
  public function varIsAssignableFromAnything($type) {
    $this->assertTrue(Type::$VAR->isAssignableFrom($type));
  }

  /**
   * Test isInstance() method on Type::$VOID
   */
  #[@test, @values('instances')]
  public function voidIsAssignableFromNothing($type) {
    $this->assertFalse(Type::$VOID->isAssignableFrom($type));
  }
}
