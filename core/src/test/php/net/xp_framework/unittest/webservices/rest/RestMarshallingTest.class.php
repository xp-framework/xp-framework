<?php namespace net\xp_framework\unittest\webservices\rest;

use lang\Type;
use lang\Primitive;
use lang\ArrayType;
use lang\MapType;
use lang\XPClass;
use lang\ClassLoader;
use util\Date;
use util\TimeZone;
use util\Money;
use util\Currency;
use lang\types\String;
use lang\types\Long;
use lang\types\Integer;
use lang\types\Short;
use lang\types\Byte;
use lang\types\Double;
use lang\types\Float;
use lang\types\Boolean;
use lang\types\ArrayList;
use lang\types\Character;
use webservices\rest\RestMarshalling;

/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestMarshalling
 */
class RestMarshallingTest extends \unittest\TestCase {
  protected $fixture= null;
  protected static $moneyMarshaller= null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new RestMarshalling();
  }

  #[@beforeClass]
  public static function defineMoneyMarshaller() {
    self::$moneyMarshaller= newinstance('webservices.rest.TypeMarshaller', array(), '{
      public function marshal($money) {
        return sprintf("%.2f %s", $money->amount()->floatValue(), $money->currency()->name());
      }

      public function unmarshal(Type $t, $input) {
        sscanf($input, "%f %s", $amount, $currency);
        return $t->newInstance($amount, Currency::getInstance($currency));
      }
    }');
  }
  
  #[@test]
  public function marshal_null() {
    $this->assertEquals(null, $this->fixture->marshal(null));
  }

  #[@test]
  public function marshal_string() {
    $this->assertEquals('Hello', $this->fixture->marshal('Hello'));
  }

  #[@test]
  public function marshal_string_wrapper_object() {
    $this->assertEquals('Hello', $this->fixture->marshal(new String('Hello')));
  }

  #[@test]
  public function marshal_char_wrapper_object() {
    $this->assertEquals('A', $this->fixture->marshal(new Character('A')));
  }

  #[@test]
  public function marshal_string_wrapper_object_unicode() {
    $this->assertEquals("\334bercoder", $this->fixture->marshal(new String("\303\234bercoder", 'utf-8')));
  }

  #[@test]
  public function marshal_int() {
    $this->assertEquals(6100, $this->fixture->marshal(6100));
  }

  #[@test]
  public function marshal_long_wrapper_object() {
    $this->assertEquals(61000, $this->fixture->marshal(new Long(61000)));
  }

  #[@test]
  public function marshal_int_wrapper_object() {
    $this->assertEquals(6100, $this->fixture->marshal(new Integer(6100)));
  }

  #[@test]
  public function marshal_short_wrapper_object() {
    $this->assertEquals(610, $this->fixture->marshal(new Short(610)));
  }

  #[@test]
  public function marshal_byte_wrapper_object() {
    $this->assertEquals(61, $this->fixture->marshal(new Byte(61)));
  }

  #[@test]
  public function marshal_double() {
    $this->assertEquals(1.5, $this->fixture->marshal(1.5));
  }

  #[@test]
  public function marshal_double_wrapper_object() {
    $this->assertEquals(1.5, $this->fixture->marshal(new Double(1.5)));
  }

  #[@test]
  public function marshal_float_wrapper_object() {
    $this->assertEquals(1.5, $this->fixture->marshal(new Float(1.5)));
  }

  #[@test]
  public function marshal_bool() {
    $this->assertEquals(true, $this->fixture->marshal(true));
  }

  #[@test]
  public function marshal_bool_wrapper_object_true() {
    $this->assertEquals(true, $this->fixture->marshal(Boolean::$TRUE));
  }

  #[@test]
  public function marshal_bool_wrapper_object_false() {
    $this->assertEquals(false, $this->fixture->marshal(Boolean::$FALSE));
  }

  #[@test]
  public function marshal_string_array() {
    $this->assertEquals(array('Hello', 'World'), $this->fixture->marshal(array('Hello', 'World')));
  }

  #[@test]
  public function marshal_string_arraylist() {
    $this->assertEquals(array('Hello', 'World'), $this->fixture->marshal(new ArrayList('Hello', 'World')));
  }

  #[@test]
  public function marshal_string_map() {
    $this->assertEquals(
      array('greeting' => 'Hello', 'name' => 'World'),
      $this->fixture->marshal(array('greeting' => 'Hello', 'name' => 'World'))
    );
  }

  #[@test]
  public function marshal_date_instance() {
    $this->assertEquals(
      '2012-12-31T18:00:00+01:00',
      $this->fixture->marshal(new Date('2012-12-31 18:00:00', new TimeZone('Europe/Berlin')))
    );
  }

  #[@test]
  public function marshal_date_array() {
    $this->assertEquals(
      array('2012-12-31T18:00:00+01:00'),
      $this->fixture->marshal(array(new Date('2012-12-31 18:00:00', new TimeZone('Europe/Berlin'))))
    );
  }

  #[@test]
  public function marshal_issue_with_field() {
    $issue= new IssueWithField(1, 'test');
    $this->assertEquals(
      array('issueId' => 1, 'title' => 'test'), 
      $this->fixture->marshal($issue)
    );
  }

  #[@test]
  public function marshal_issue_with_getter() {
    $issue= new IssueWithGetter(1, 'test');
    $this->assertEquals(
      array('issueId' => 1, 'title' => 'test', 'createdAt' => null), 
      $this->fixture->marshal($issue)
    );
  }

  #[@test]
  public function marshal_array_of_issues() {
    $issues= array(
      new IssueWithField(1, 'test1'),
      new IssueWithField(2, 'test2')
    );
    $this->assertEquals(
      array(array('issueId' => 1, 'title' => 'test1'), array('issueId' => 2, 'title' => 'test2')),
      $this->fixture->marshal($issues)
    );
  }

  #[@test]
  public function marshal_map_of_issues() {
    $issues= array(
      'one' => new IssueWithField(1, 'test1'),
      'two' => new IssueWithField(2, 'test2')
    );
    $this->assertEquals(
      array('one' => array('issueId' => 1, 'title' => 'test1'), 'two' => array('issueId' => 2, 'title' => 'test2')),
      $this->fixture->marshal($issues)
    );
  }

  #[@test]
  public function marshal_static_member_excluded() {
    $o= newinstance('lang.Object', array(), '{
      public $name= "Test";
      public static $instance;
    }');
    $this->assertEquals(array('name' => 'Test'), $this->fixture->marshal($o));
  }

  #[@test]
  public function marshal_money() {
    $this->fixture->addMarshaller('util.Money', self::$moneyMarshaller);
    $this->assertEquals(
      '6.10 USD',
      $this->fixture->marshal(new Money(6.10, Currency::$USD))
    );
  }

  #[@test]
  public function marshal_array_of_money() {
    $this->fixture->addMarshaller('util.Money', self::$moneyMarshaller);
    $this->assertEquals(
      array('6.10 USD'),
      $this->fixture->marshal(array(new Money(6.10, Currency::$USD)))
    );
  }

  #[@test]
  public function unmarshal_null() {
    $this->assertEquals(null, $this->fixture->unmarshal(Type::$VAR, null));
  }

  #[@test]
  public function unmarshal_null_as_string() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$STRING, null));
  }

  #[@test]
  public function unmarshal_null_as_int() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$INT, null));
  }

  #[@test]
  public function unmarshal_null_as_double() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$DOUBLE, null));
  }

  #[@test]
  public function unmarshal_null_as_bool() {
    $this->assertEquals(null, $this->fixture->unmarshal(Primitive::$BOOL, null));
  }

  #[@test]
  public function unmarshal_string() {
    $this->assertEquals('Test', $this->fixture->unmarshal(Primitive::$STRING, 'Test'));
  }

  #[@test]
  public function unmarshal_int_as_string() {
    $this->assertEquals('1', $this->fixture->unmarshal(Primitive::$STRING, 1));
  }

  #[@test]
  public function unmarshal_double_as_string() {
    $this->assertEquals('1', $this->fixture->unmarshal(Primitive::$STRING, 1.0));
  }

  #[@test]
  public function unmarshal_bool_as_string() {
    $this->assertEquals('1', $this->fixture->unmarshal(Primitive::$STRING, true));
    $this->assertEquals('', $this->fixture->unmarshal(Primitive::$STRING, false));
  }

  #[@test]
  public function unmarshal_array_as_string() {
    $this->assertEquals('Test', $this->fixture->unmarshal(Primitive::$STRING, array('Test')));
  }

  #[@test]
  public function unmarshal_map_as_string() {
    $this->assertEquals('Test', $this->fixture->unmarshal(Primitive::$STRING, array('name' => 'Test')));
  }

  #[@test]
  public function unmarshal_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, 1));
  }

  #[@test]
  public function unmarshal_string_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, '1'));
  }

  #[@test]
  public function unmarshal_double_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, 1.0));
  }

  #[@test]
  public function unmarshal_bool_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, true));
    $this->assertEquals(0, $this->fixture->unmarshal(Primitive::$INT, false));
  }

  #[@test]
  public function unmarshal_array_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, array(1)));
  }

  #[@test]
  public function unmarshal_map_as_int() {
    $this->assertEquals(1, $this->fixture->unmarshal(Primitive::$INT, array('one' => 1)));
  }

  #[@test]
  public function unmarshal_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, 1.0));
  }

  #[@test]
  public function unmarshal_string_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, '1.0'));
  }

  #[@test]
  public function unmarshal_int_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, 1));
  }

  #[@test]
  public function unmarshal_bool_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, true));
    $this->assertEquals(0.0, $this->fixture->unmarshal(Primitive::$DOUBLE, false));
  }

  #[@test]
  public function unmarshal_array_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, array(1.0)));
  }

  #[@test]
  public function unmarshal_map_as_double() {
    $this->assertEquals(1.0, $this->fixture->unmarshal(Primitive::$DOUBLE, array('one' => 1.0)));
  }

  #[@test]
  public function unmarshal_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, true));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, false));
  }

  #[@test]
  public function unmarshal_int_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, 1));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, 0));
  }

  #[@test]
  public function unmarshal_double_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, 1.0));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, 0.0));
  }

  #[@test]
  public function unmarshal_string_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, 'non-empty'));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, ''));
  }

  #[@test]
  public function unmarshal_array_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, array(true)));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, array(false)));
  }

  #[@test]
  public function unmarshal_map_as_bool() {
    $this->assertEquals(true, $this->fixture->unmarshal(Primitive::$BOOL, array('one' => true)));
    $this->assertEquals(false, $this->fixture->unmarshal(Primitive::$BOOL, array('one' => false)));
  }

  #[@test]
  public function unmarshal_var_array() {
    $this->assertEquals(
      array(1, 2, 3), 
      $this->fixture->unmarshal(ArrayType::forName('var[]'), array(1, 2, 3))
    );
  }

  #[@test]
  public function unmarshal_int_array() {
    $this->assertEquals(
      array(1, 2, 3), 
      $this->fixture->unmarshal(ArrayType::forName('int[]'), array(1, '2', 3.0))
    );
  }

  #[@test]
  public function unmarshal_var_map() {
    $this->assertEquals(
      array('one' => 1, 'two' => 2, 'three' => 3),
      $this->fixture->unmarshal(MapType::forName('[:var]'), array('one' => 1, 'two' => 2, 'three' => 3))
    );
  }

  #[@test]
  public function unmarshal_int_map() {
    $this->assertEquals(
      array('one' => 1, 'two' => 2, 'three' => 3),
      $this->fixture->unmarshal(MapType::forName('[:int]'), array('one' => 1, 'two' => '2', 'three' => 3.0))
    );
  }

  #[@test]
  public function unmarshal_issue_with_field() {
    $issue= new IssueWithField(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function unmarshal_issue_with_underscore_field() {
    $issue= new IssueWithUnderscoreField(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function unmarshal_issue_with_setter() {
    $issue= new IssueWithSetter(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function unmarshal_issue_with_underscore_setter() {
    $issue= new IssueWithUnderscoreSetter(1, 'test');
    $this->assertEquals(
      $issue, 
      $this->fixture->unmarshal($issue->getClass(), array('issue_id' => 1, 'title' => 'test'))
    );
  }

  #[@test]
  public function unmarshal_array_of_issues() {
    $issue1= new IssueWithField(1, 'test1');
    $issue2= new IssueWithField(2, 'test2');
    $this->assertEquals(array($issue1, $issue2), $this->fixture->unmarshal(
      ArrayType::forName($issue1->getClassName().'[]'), 
      array(array('issue_id' => 1, 'title' => 'test1'), array('issue_id' => 2, 'title' => 'test2')))
    );
  }

  #[@test]
  public function unmarshal_map_of_issues() {
    $issue1= new IssueWithField(1, 'test1');
    $issue2= new IssueWithField(2, 'test2');
    $this->assertEquals(array('one' => $issue1, 'two' => $issue2), $this->fixture->unmarshal(
      MapType::forName('[:'.$issue1->getClassName().']'), 
      array('one' => array('issue_id' => 1, 'title' => 'test1'), 'two' => array('issue_id' => 2, 'title' => 'test2')))
    );
  }

  #[@test]
  public function unmarshal_no_constructor() {
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
  public function unmarshal_static_valueof_method() {
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
  public function unmarshal_public_valueof_instance_method_not_invoked() {
    $class= ClassLoader::defineClass('RestConversionTest_PublicValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      public function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->unmarshal($class, array('id' => 4711)));
  }

  #[@test]
  public function unmarshal_private_valueof_instance_method_not_invoked() {
    $class= ClassLoader::defineClass('RestConversionTest_PrivateValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      private static function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->unmarshal($class, array('id' => 4711)));
  }

  #[@test]
  public function unmarshal_protected_valueof_instance_method_not_invoked() {
    $class= ClassLoader::defineClass('RestConversionTest_ProtectedValueOf', 'net.xp_framework.unittest.webservices.rest.ConstructorFixture', array(), '{
      protected static function valueOf($id) { throw new IllegalStateException("Should not reach this point!"); }
    }');
    $c= $class->newInstance();
    $c->id= 4711;
    $this->assertEquals($c, $this->fixture->unmarshal($class, array('id' => 4711)));
  }

  #[@test]
  public function unmarshal_date_iso_formatted() {
    $this->assertEquals(
      new Date('2009-04-12T20:44:55'), 
      $this->fixture->unmarshal(XPClass::forName('util.Date'), '2009-04-12T20:44:55')
    );
  }

  #[@test]
  public function unmarshal_constructor_not_used_with_complex_payload() {
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
  public function unmarshal_valueof_not_used_with_complex_payload() {
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
  public function unmarshal_static_member_excluded() {
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
  public function unmarshal_string_wrapper() {
    $this->assertEquals(
      new String('Hello'),
      $this->fixture->unmarshal(Primitive::$STRING->wrapperClass(), 'Hello')
    );
  }

  #[@test]
  public function unmarshal_integer_wrapper() {
    $this->assertEquals(
      new Integer(5),
      $this->fixture->unmarshal(Primitive::$INT->wrapperClass(), 5)
    );
  }

  #[@test]
  public function unmarshal_double_wrapper() {
    $this->assertEquals(
      new Double(5.0),
      $this->fixture->unmarshal(Primitive::$DOUBLE->wrapperClass(), 5.0)
    );
  }

  #[@test]
  public function unmarshal_bool_wrapper() {
    $this->assertEquals(
      new Boolean(true),
      $this->fixture->unmarshal(Primitive::$BOOL->wrapperClass(), true)
    );
  }

  #[@test]
  public function unmarshal_money() {
    $this->fixture->addMarshaller('util.Money', self::$moneyMarshaller);
    $this->assertEquals(
      new Money(6.10, Currency::$USD),
      $this->fixture->unmarshal(XPClass::forName('util.Money'), '6.10 USD')
    );
  }

  #[@test]
  public function unmarshal_array_of_money() {
    $this->fixture->addMarshaller('util.Money', self::$moneyMarshaller);
    $this->assertEquals(
      array(new Money(6.10, Currency::$USD)),
      $this->fixture->unmarshal(ArrayType::forName('util.Money[]'), array('6.10 USD'))
    );
  }
}
