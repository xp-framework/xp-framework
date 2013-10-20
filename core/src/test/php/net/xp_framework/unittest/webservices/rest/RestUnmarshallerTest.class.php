<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestMarshalling;
use lang\Type;
use lang\Primitive;
use lang\ArrayType;
use lang\MapType;
use lang\XPClass;
use lang\ClassLoader;
use util\Date;
use lang\types\String;
use lang\types\Long;
use lang\types\Integer;
use lang\types\Short;
use lang\types\Byte;
use lang\types\Double;
use lang\types\Float;
use lang\types\ArrayList;
use lang\types\Boolean;

/**
 * Sets up test case
 *
 * @see   xp://webservices.rest.RestMarshalling
 */
class RestUnmarshallerTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new RestMarshalling();
  }
  
  #[@test]
  public function null() {
    $this->assertEquals(null, $this->fixture->unmarshal(Type::$VAR, null));
  }

  #[@test]
  public function null_as_string() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$STRING, null));
  }

  #[@test]
  public function null_as_int() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$INT, null));
  }

  #[@test]
  public function null_as_double() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$DOUBLE, null));
  }

  #[@test]
  public function null_as_bool() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$BOOL, null));
  }

  #[@test]
  public function string() {
    $this->assertEquals('Test', $this->fixture->unmarshal(Primitive::$STRING, 'Test'));
  }

  #[@test]
  public function int_as_string() {
    $this->assertEquals('1', $this->fixture->unmarshal(Primitive::$STRING, 1));
  }

  #[@test]
  public function double_as_string() {
    $this->assertEquals('1', $this->fixture->unmarshal(Primitive::$STRING, 1.0));
  }

  #[@test]
  public function bool_as_string() {
    $this->assertEquals('1', $this->fixture->unmarshal(Primitive::$STRING, true));
    $this->assertEquals('', $this->fixture->unmarshal(Primitive::$STRING, false));
  }

  #[@test]
  public function array_as_string() {
    $this->assertEquals('Test', $this->fixture->unmarshal(Primitive::$STRING, array('Test')));
  }

  #[@test]
  public function map_as_string() {
    $this->assertEquals('Test', $this->fixture->unmarshal(Primitive::$STRING, array('name' => 'Test')));
  }

  #[@test]
  public function int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, 1));
  }

  #[@test]
  public function string_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, '1'));
  }

  #[@test]
  public function double_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, 1.0));
  }

  #[@test]
  public function bool_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, true));
    $this->assertEquals(0, $this->fixture->unmarshal(Primitive::$INT, false));
  }

  #[@test]
  public function array_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, array(1)));
  }

  #[@test]
  public function map_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, array('one' => 1)));
  }

  #[@test]
  public function double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, 1.0));
  }

  #[@test]
  public function string_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, '1.0'));
  }

  #[@test]
  public function int_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, 1));
  }

  #[@test]
  public function bool_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, true));
    $this->assertEquals(0.0, $this->fixture->unmarshal(Primitive::$DOUBLE, false));
  }

  #[@test]
  public function array_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, array(1.0)));
  }

  #[@test]
  public function map_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, array('one' => 1.0)));
  }

  #[@test]
  public function bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, true));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, false));
  }

  #[@test]
  public function int_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, 1));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, 0));
  }

  #[@test]
  public function double_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, 1.0));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, 0.0));
  }

  #[@test]
  public function string_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, 'non-empty'));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, ''));
  }

  #[@test]
  public function array_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, array(true)));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, array(false)));
  }

  #[@test]
  public function map_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, array('one' => true)));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, array('one' => false)));
  }

  #[@test]
  public function var_array() {
    $this->assertEquals(
      array(1, 2, 3), 
      $this->fixture->unmarshal(ArrayType::forName('var[]'), array(1, 2, 3))
    );
  }

  #[@test]
  public function int_array() {
    $this->assertEquals(
      array(1, 2, 3), 
      $this->fixture->unmarshal(ArrayType::forName('int[]'), array(1, '2', 3.0))
    );
  }

  #[@test]
  public function var_map() {
    $this->assertEquals(
      array('one' => 1, 'two' => 2, 'three' => 3),
      $this->fixture->unmarshal(MapType::forName('[:var]'), array('one' => 1, 'two' => 2, 'three' => 3))
    );
  }

  #[@test]
  public function int_map() {
    $this->assertEquals(
      array('one' => 1, 'two' => 2, 'three' => 3),
      $this->fixture->unmarshal(MapType::forName('[:int]'), array('one' => 1, 'two' => '2', 'three' => 3.0))
    );
  }

  #[@test]
  public function issue_with_field() {
    $issue= new IssueWithField(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function issue_with_underscore_field() {
    $issue= new IssueWithUnderscoreField(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function issue_with_setter() {
    $issue= new IssueWithSetter(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function issue_with_underscore_setter() {
    $issue= new IssueWithUnderscoreSetter(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function array_of_issues() {
    $issue1= new IssueWithField(1, 'test1');
    $issue2= new IssueWithField(2, 'test2');
    $this->assertEquals(array($issue1, $issue2), $this->fixture->unmarshal(
      ArrayType::forName($issue1->getClassName().'[]'), 
      array(array('issue_id' => 1, 'title' => 'test1'), array('issue_id' => 2, 'title' => 'test2')))
    );
  }

  #[@test]
  public function map_of_issues() {
    $issue1= new IssueWithField(1, 'test1');
    $issue2= new IssueWithField(2, 'test2');
    $this->assertEquals(array('one' => $issue1, 'two' => $issue2), $this->fixture->unmarshal(
      MapType::forName('[:'.$issue1->getClassName().']'), 
      array('one' => array('issue_id' => 1, 'title' => 'test1'), 'two' => array('issue_id' => 2, 'title' => 'test2')))
    );
  }

  #[@test]
  public function no_constructor() {
    $class= ClassLoader::defineClass('RestConversionTest_NoConstructor', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals(
      $c,
      $this->fixture->unmarshal($class, array('id' => 4711))
    );
  }

  #[@test]
  public function static_valueof_method() {
    $class= ClassLoader::defineClass('RestConversionTest_StaticValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      protected function __construct($id) { $this->id= (int)$id; }
      public static function valueOf($id) { return new self($id); }
    }');
    $this->assertEquals(
      $class->getMethod('valueOf')->invoke(null, array(4711)),
      $this->fixture->unmarshal($class, array('id' => 4711))
    );
  }

  #[@test]
  public function public_valueof_instance_method_not_invoked() {
    $class= ClassLoader::defineClass('RestConversionTest_PublicValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      public function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->unmarshal($class, array('id' => 4711)));
  }

  #[@test]
  public function private_valueof_instance_method_not_invoked() {
    $class= ClassLoader::defineClass('RestConversionTest_PrivateValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      private static function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->unmarshal($class, array('id' => 4711)));
  }

  #[@test]
  public function protected_valueof_instance_method_not_invoked() {
    $class= ClassLoader::defineClass('RestConversionTest_ProtectedValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      protected static function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->unmarshal($class, array('id' => 4711)));
  }

  #[@test]
  public function date_iso_formatted() {
    $this->assertEquals(
      new Date('2009-04-12T20:44:55'), 
      $this->fixture->unmarshal(XPClass::forName('util.Date'), '2009-04-12T20:44:55')
    );
  }

  #[@test]
  public function constructor_not_used_with_complex_payload() {
    $class= ClassLoader::defineClass('RestConversionTest_ConstructorVsSetter', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
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
      $this->fixture->unmarshal($class, array('id' => 4711, 'name' => 'Test'))
    );
  }

  #[@test]
  public function valueof_not_used_with_complex_payload() {
    $class= ClassLoader::defineClass('RestConversionTest_ValueOfVsSetter', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
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
      $this->fixture->unmarshal($class, array('id' => 4711, 'name' => 'Test'))
    );
  }

  #[@test]
  public function static_member_excluded() {
    $class= ClassLoader::defineClass('RestConversionTest_StaticMemberExcluded', 'lang.Object', array(), '{
      public $name;
      public static $instance= null;
    }');
    $this->assertnull($this->fixture->unmarshal($class, array('name' => 'Test', 'instance' => 'Value'))
      ->getClass()
      ->getField('instance')
      ->get(null)
    );
  }

  #[@test]
  public function string_wrapper() {
    $this->assertEquals(
      new String('Hello'),
      $this->fixture->unmarshal(Primitive::$STRING->wrapperClass(), 'Hello')
    );
  }

  #[@test]
  public function integer_wrapper() {
    $this->assertEquals(
      new Integer(5),
      $this->fixture->unmarshal(Primitive::$INT->wrapperClass(), 5)
    );
  }

  #[@test]
  public function double_wrapper() {
    $this->assertEquals(
      new Double(5.0),
      $this->fixture->unmarshal(Primitive::$DOUBLE->wrapperClass(), 5.0)
    );
  }

  #[@test]
  public function bool_wrapper() {
    $this->assertEquals(
      new Boolean(true),
      $this->fixture->unmarshal(Primitive::$BOOL->wrapperClass(), true)
    );
  }
}
