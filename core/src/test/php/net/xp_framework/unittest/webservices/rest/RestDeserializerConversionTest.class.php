<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestDeserializer;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestDeserializer
 */
class RestDeserializerConversionTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= newinstance('RestDeserializer', array(), '{
      public function deserialize($in, $type) { /* Intentionally empty */ }
    }');
  }
  
  /**
   * Test null
   *
   */
  #[@test]
  public function null() {
    $this->assertEquals(null, $this->fixture->convert(\lang\Type::$VAR, null));
  }

  /**
   * Test null in string context
   *
   */
  #[@test]
  public function null_as_string() {
    $this->assertEquals(null, $this->fixture->convert(\lang\Primitive::$STRING, null));
  }

  /**
   * Test null in int context
   *
   */
  #[@test]
  public function null_as_int() {
    $this->assertEquals(null, $this->fixture->convert(\lang\Primitive::$INT, null));
  }

  /**
   * Test null in double context
   *
   */
  #[@test]
  public function null_as_double() {
    $this->assertEquals(null, $this->fixture->convert(\lang\Primitive::$DOUBLE, null));
  }

  /**
   * Test null in bool context
   *
   */
  #[@test]
  public function null_as_bool() {
    $this->assertEquals(null, $this->fixture->convert(\lang\Primitive::$BOOL, null));
  }

  /**
   * Test string
   *
   */
  #[@test]
  public function string() {
    $this->assertEquals('Test', $this->fixture->convert(\lang\Primitive::$STRING, 'Test'));
  }

  /**
   * Test string
   *
   */
  #[@test]
  public function int_as_string() {
    $this->assertEquals('1', $this->fixture->convert(\lang\Primitive::$STRING, 1));
  }

  /**
   * Test string
   *
   */
  #[@test]
  public function double_as_string() {
    $this->assertEquals('1', $this->fixture->convert(\lang\Primitive::$STRING, 1.0));
  }

  /**
   * Test string
   *
   */
  #[@test]
  public function bool_as_string() {
    $this->assertEquals('1', $this->fixture->convert(\lang\Primitive::$STRING, true));
    $this->assertEquals('', $this->fixture->convert(\lang\Primitive::$STRING, false));
  }

  /**
   * Test string
   *
   */
  #[@test]
  public function array_as_string() {
    $this->assertEquals('Test', $this->fixture->convert(\lang\Primitive::$STRING, array('Test')));
  }

  /**
   * Test string
   *
   */
  #[@test]
  public function map_as_string() {
    $this->assertEquals('Test', $this->fixture->convert(\lang\Primitive::$STRING, array('name' => 'Test')));
  }

  /**
   * Test int
   *
   */
  #[@test]
  public function int() {
    $this->assertEquals(1, $this->fixture->convert(\lang\Primitive::$INT, 1));
  }

  /**
   * Test strings as ints
   *
   */
  #[@test]
  public function string_as_int() {
    $this->assertEquals(1, $this->fixture->convert(\lang\Primitive::$INT, '1'));
  }

  /**
   * Test doubles as ints
   *
   */
  #[@test]
  public function double_as_int() {
    $this->assertEquals(1, $this->fixture->convert(\lang\Primitive::$INT, 1.0));
  }

  /**
   * Test bools as doubles
   *
   */
  #[@test]
  public function bool_as_int() {
    $this->assertEquals(1, $this->fixture->convert(\lang\Primitive::$INT, true));
    $this->assertEquals(0, $this->fixture->convert(\lang\Primitive::$INT, false));
  }

  /**
   * Test int
   *
   */
  #[@test]
  public function array_as_int() {
    $this->assertEquals(1, $this->fixture->convert(\lang\Primitive::$INT, array(1)));
  }

  /**
   * Test int
   *
   */
  #[@test]
  public function map_as_int() {
    $this->assertEquals(1, $this->fixture->convert(\lang\Primitive::$INT, array('one' => 1)));
  }

  /**
   * Test double
   *
   */
  #[@test]
  public function double() {
    $this->assertEquals(1.0, $this->fixture->convert(\lang\Primitive::$DOUBLE, 1.0));
  }

  /**
   * Test strings as doubles
   *
   */
  #[@test]
  public function string_as_double() {
    $this->assertEquals(1.0, $this->fixture->convert(\lang\Primitive::$DOUBLE, '1.0'));
  }

  /**
   * Test ints as doubles
   *
   */
  #[@test]
  public function int_as_double() {
    $this->assertEquals(1.0, $this->fixture->convert(\lang\Primitive::$DOUBLE, 1));
  }

  /**
   * Test bools as doubles
   *
   */
  #[@test]
  public function bool_as_double() {
    $this->assertEquals(1.0, $this->fixture->convert(\lang\Primitive::$DOUBLE, true));
    $this->assertEquals(0.0, $this->fixture->convert(\lang\Primitive::$DOUBLE, false));
  }

  /**
   * Test double
   *
   */
  #[@test]
  public function array_as_double() {
    $this->assertEquals(1.0, $this->fixture->convert(\lang\Primitive::$DOUBLE, array(1.0)));
  }

  /**
   * Test double
   *
   */
  #[@test]
  public function map_as_double() {
    $this->assertEquals(1.0, $this->fixture->convert(\lang\Primitive::$DOUBLE, array('one' => 1.0)));
  }

  /**
   * Test bool
   *
   */
  #[@test]
  public function bool() {
    $this->assertEquals(true, $this->fixture->convert(\lang\Primitive::$BOOL, true));
    $this->assertEquals(false, $this->fixture->convert(\lang\Primitive::$BOOL, false));
  }

  /**
   * Test bool
   *
   */
  #[@test]
  public function int_as_bool() {
    $this->assertEquals(true, $this->fixture->convert(\lang\Primitive::$BOOL, 1));
    $this->assertEquals(false, $this->fixture->convert(\lang\Primitive::$BOOL, 0));
  }

  /**
   * Test bool
   *
   */
  #[@test]
  public function double_as_bool() {
    $this->assertEquals(true, $this->fixture->convert(\lang\Primitive::$BOOL, 1.0));
    $this->assertEquals(false, $this->fixture->convert(\lang\Primitive::$BOOL, 0.0));
  }

  /**
   * Test bool
   *
   */
  #[@test]
  public function string_as_bool() {
    $this->assertEquals(true, $this->fixture->convert(\lang\Primitive::$BOOL, 'non-empty'));
    $this->assertEquals(false, $this->fixture->convert(\lang\Primitive::$BOOL, ''));
  }

  /**
   * Test bool
   *
   */
  #[@test]
  public function array_as_bool() {
    $this->assertEquals(true, $this->fixture->convert(\lang\Primitive::$BOOL, array(true)));
    $this->assertEquals(false, $this->fixture->convert(\lang\Primitive::$BOOL, array(false)));
  }

  /**
   * Test bool
   *
   */
  #[@test]
  public function map_as_bool() {
    $this->assertEquals(true, $this->fixture->convert(\lang\Primitive::$BOOL, array('one' => true)));
    $this->assertEquals(false, $this->fixture->convert(\lang\Primitive::$BOOL, array('one' => false)));
  }

  /**
   * Test var-array
   *
   */
  #[@test]
  public function var_array() {
    $this->assertEquals(
      array(1, 2, 3), 
      $this->fixture->convert(\lang\ArrayType::forName('var[]'), array(1, 2, 3))
    );
  }

  /**
   * Test var-array
   *
   */
  #[@test]
  public function int_array() {
    $this->assertEquals(
      array(1, 2, 3), 
      $this->fixture->convert(\lang\ArrayType::forName('int[]'), array(1, '2', 3.0))
    );
  }

  /**
   * Test var-map
   *
   */
  #[@test]
  public function var_map() {
    $this->assertEquals(
      array('one' => 1, 'two' => 2, 'three' => 3),
      $this->fixture->convert(\lang\MapType::forName('[:var]'), array('one' => 1, 'two' => 2, 'three' => 3))
    );
  }

  /**
   * Test int-map
   *
   */
  #[@test]
  public function int_map() {
    $this->assertEquals(
      array('one' => 1, 'two' => 2, 'three' => 3),
      $this->fixture->convert(\lang\MapType::forName('[:int]'), array('one' => 1, 'two' => '2', 'three' => 3.0))
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function issue_with_field() {
    $issue= new IssueWithField(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->convert($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function issue_with_underscore_field() {
    $issue= new IssueWithUnderscoreField(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->convert($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function issue_with_setter() {
    $issue= new IssueWithSetter(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->convert($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function issue_with_underscore_setter() {
    $issue= new IssueWithUnderscoreSetter(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->convert($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function array_of_issues() {
    $issue1= new IssueWithField(1, 'test1');
    $issue2= new IssueWithField(2, 'test2');
    $this->assertEquals(array($issue1, $issue2), $this->fixture->convert(
      \lang\ArrayType::forName($issue1->getClassName().'[]'), 
      array(array('issue_id' => 1, 'title' => 'test1'), array('issue_id' => 2, 'title' => 'test2')))
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function map_of_issues() {
    $issue1= new IssueWithField(1, 'test1');
    $issue2= new IssueWithField(2, 'test2');
    $this->assertEquals(array('one' => $issue1, 'two' => $issue2), $this->fixture->convert(
      \lang\MapType::forName('[:'.$issue1->getClassName().']'), 
      array('one' => array('issue_id' => 1, 'title' => 'test1'), 'two' => array('issue_id' => 2, 'title' => 'test2')))
    );
  }

  /**
   * Test value object's constructor is called
   *
   */
  #[@test]
  public function no_constructor() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_NoConstructor', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals(
      $c,
      $this->fixture->convert($class, array('id' => 4711))
    );
  }

  /**
   * Test value object's constructor is called
   *
   */
  #[@test]
  public function static_valueof_method() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_StaticValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      protected function __construct($id) { $this->id= (int)$id; }
      public static function valueOf($id) { return new self($id); }
    }');
    $this->assertEquals(
      $class->getMethod('valueOf')->invoke(null, array(4711)),
      $this->fixture->convert($class, array('id' => 4711))
    );
  }

  /**
   * Test value object's constructor is called
   *
   */
  #[@test]
  public function public_valueof_instance_method_not_invoked() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_PublicValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      public function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->convert($class, array('id' => 4711)));
  }

  /**
   * Test value object's constructor is called
   *
   */
  #[@test]
  public function private_valueof_instance_method_not_invoked() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_PrivateValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      private static function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->convert($class, array('id' => 4711)));
  }

  /**
   * Test value object's constructor is called
   *
   */
  #[@test]
  public function protected_valueof_instance_method_not_invoked() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_ProtectedValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      protected static function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->convert($class, array('id' => 4711)));
  }

  /**
   * Test Date class
   *
   */
  #[@test]
  public function date_iso_formatted() {
    $this->assertEquals(
      new \util\Date('2009-04-12T20:44:55'), 
      $this->fixture->convert(\lang\XPClass::forName('util.Date'), '2009-04-12T20:44:55')
    );
  }

  /**
   * Test value object's constructor is not called with the payload if that
   * has more than just one element (e.g. { "id" : 4711, "name" : "Test"}).
   *
   * If it was, the "id" and "name" memberswould never be set in the
   * following example:
   * 
   * <code>
   *   class ValueObject extends Object { 
   *     public $id, $name;
   *
   *     public function __construct($id= NULL) { ... }
   *   }
   * </code>
   */
  #[@test]
  public function constructor_not_used_with_complex_payload() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_ConstructorVsSetter', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      public $name;
      public function __construct() { 
        if (func_num_args() > 0) throw new IllegalStateException("Should not reach this point!");
      }
      public function withId($id) { $this->id= $id; return $this; }
      public function withName($name) { $this->name= $name; return $this; }
      public function equals($cmp) { return parent::equals($cmp) && $this->name === $cmp->name; }
      public function toString() { return parent::toString()."(name=\'".$this->name."\')"; }
    }');
    $this->assertEquals(
      $class->newInstance()->withId(4711)->withName('Test'),
      $this->fixture->convert($class, array('id' => 4711, 'name' => 'Test'))
    );
  }

  /**
   * Test value object's constructor is not called with the payload if that
   * has more than just one element (e.g. { "id" : 4711, "name" : "Test"}).
   *
   * If it was, the "id" and "name" memberswould never be set in the
   * following example:
   * 
   * <code>
   *   class ValueObject extends Object { 
   *     public $id, $name;
   *
   *     public static function valueOf($id) { ... }
   *   }
   * </code>
   */
  #[@test]
  public function valueof_not_used_with_complex_payload() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_ValueOfVsSetter', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      public $name;
      public static function valueOf($id) { 
        throw new IllegalStateException("Should not reach this point!");
      }
      public function withId($id) { $this->id= $id; return $this; }
      public function withName($name) { $this->name= $name; return $this; }
      public function equals($cmp) { return parent::equals($cmp) && $this->name === $cmp->name; }
      public function toString() { return parent::toString()."(name=\'".$this->name."\')"; }
    }');
    $this->assertEquals(
      $class->newInstance()->withId(4711)->withName('Test'),
      $this->fixture->convert($class, array('id' => 4711, 'name' => 'Test'))
    );
  }

  /**
   * Test static members
   *
   */
  #[@test]
  public function static_member_excluded() {
    $class= \lang\ClassLoader::defineClass('RestConversionTest_StaticMemberExcluded', 'lang.Object', array(), '{
      public $name;
      public static $instance= NULL;
    }');
    $this->assertNull($this->fixture->convert($class, array('name' => 'Test', 'instance' => 'Value'))
      ->getClass()
      ->getField('instance')
      ->get(null)
    );
  }

  /**
   * Test string wrapper type
   *
   */
  #[@test]
  public function string_wrapper() {
    $this->assertEquals(
      new \lang\types\String('Hello'),
      $this->fixture->convert(\lang\Primitive::$STRING->wrapperClass(), 'Hello')
    );
  }

  /**
   * Test integer wrapper type
   *
   */
  #[@test]
  public function integer_wrapper() {
    $this->assertEquals(
      new \lang\types\Integer(5),
      $this->fixture->convert(\lang\Primitive::$INT->wrapperClass(), 5)
    );
  }

  /**
   * Test double wrapper type
   *
   */
  #[@test]
  public function double_wrapper() {
    $this->assertEquals(
      new \lang\types\Double(5.0),
      $this->fixture->convert(\lang\Primitive::$DOUBLE->wrapperClass(), 5.0)
    );
  }

  /**
   * Test bool wrapper type
   *
   */
  #[@test]
  public function bool_wrapper() {
    $this->assertEquals(
      new \lang\types\Boolean(true),
      $this->fixture->convert(\lang\Primitive::$BOOL->wrapperClass(), true)
    );
  }
}
