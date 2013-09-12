<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use lang\types\String;
use lang\types\Long;
use lang\types\Integer;
use lang\types\Short;
use lang\types\Byte;
use lang\types\Double;
use lang\types\Float;
use lang\types\ArrayList;
use webservices\rest\RestSerializer;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestSerializer
 */
class RestSerializerConversionTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= newinstance('RestSerializer', array(), '{
      public function contentType() { /* Intentionally empty */ }
      public function serialize($payload) { /* Intentionally empty */ }
    }');
  }
  
  /**
   * Test null
   *
   */
  #[@test]
  public function null() {
    $this->assertEquals(null, $this->fixture->convert(null));
  }

  /**
   * Test a string
   *
   */
  #[@test]
  public function string() {
    $this->assertEquals('Hello', $this->fixture->convert('Hello'));
  }

  /**
   * Test a string
   *
   */
  #[@test]
  public function string_wrapper_object() {
    $this->assertEquals('Hello', $this->fixture->convert(new String('Hello')));
  }

  /**
   * Test a string
   *
   */
  #[@test]
  public function char_wrapper_object() {
    $this->assertEquals('A', $this->fixture->convert(new \lang\types\Character('A')));
  }

  /**
   * Test a string
   *
   */
  #[@test]
  public function string_wrapper_object_unicode() {
    $this->assertEquals("\334bercoder", $this->fixture->convert(new String("\303\234bercoder", 'utf-8')));
  }

  /**
   * Test an integer
   *
   */
  #[@test]
  public function int() {
    $this->assertEquals(6100, $this->fixture->convert(6100));
  }

  /**
   * Test an integer
   *
   */
  #[@test]
  public function long_wrapper_object() {
    $this->assertEquals(61000, $this->fixture->convert(new Long(61000)));
  }

  /**
   * Test an integer
   *
   */
  #[@test]
  public function int_wrapper_object() {
    $this->assertEquals(6100, $this->fixture->convert(new Integer(6100)));
  }

  /**
   * Test a short
   *
   */
  #[@test]
  public function short_wrapper_object() {
    $this->assertEquals(610, $this->fixture->convert(new Short(610)));
  }

  /**
   * Test a byte
   *
   */
  #[@test]
  public function byte_wrapper_object() {
    $this->assertEquals(61, $this->fixture->convert(new Byte(61)));
  }

  /**
   * Test a double
   *
   */
  #[@test]
  public function double() {
    $this->assertEquals(1.5, $this->fixture->convert(1.5));
  }

  /**
   * Test a double
   *
   */
  #[@test]
  public function double_wrapper_object() {
    $this->assertEquals(1.5, $this->fixture->convert(new Double(1.5)));
  }

  /**
   * Test a float
   *
   */
  #[@test]
  public function float_wrapper_object() {
    $this->assertEquals(1.5, $this->fixture->convert(new Float(1.5)));
  }

  /**
   * Test a boolean
   *
   */
  #[@test]
  public function bool() {
    $this->assertEquals(true, $this->fixture->convert(true));
  }

  /**
   * Test a boolean
   *
   */
  #[@test]
  public function bool_wrapper_object_true() {
    $this->assertEquals(true, $this->fixture->convert(\lang\types\Boolean::$TRUE));
  }

  /**
   * Test a boolean
   *
   */
  #[@test]
  public function bool_wrapper_object_false() {
    $this->assertEquals(false, $this->fixture->convert(\lang\types\Boolean::$FALSE));
  }

  /**
   * Test an array of strings
   *
   */
  #[@test]
  public function string_array() {
    $this->assertEquals(array('Hello', 'World'), $this->fixture->convert(array('Hello', 'World')));
  }

  /**
   * Test an array of strings
   *
   */
  #[@test]
  public function string_arraylist() {
    $this->assertEquals(array('Hello', 'World'), $this->fixture->convert(new ArrayList('Hello', 'World')));
  }

  /**
   * Test an array of strings
   *
   */
  #[@test]
  public function string_map() {
    $this->assertEquals(
      array('greeting' => 'Hello', 'name' => 'World'),
      $this->fixture->convert(array('greeting' => 'Hello', 'name' => 'World'))
    );
  }

  /**
   * Test a date instance
   *
   */
  #[@test]
  public function date_instance() {
    $this->assertEquals(
      '2012-12-31T18:00:00+01:00',
      $this->fixture->convert(new \util\Date('2012-12-31 18:00:00', new \util\TimeZone('Europe/Berlin')))
    );
  }

  /**
   * Test a date instance
   *
   */
  #[@test]
  public function date_array() {
    $this->assertEquals(
      array('2012-12-31T18:00:00+01:00'),
      $this->fixture->convert(array(new \util\Date('2012-12-31 18:00:00', new \util\TimeZone('Europe/Berlin'))))
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
      array('issueId' => 1, 'title' => 'test'), 
      $this->fixture->convert($issue)
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function issue_with_getter() {
    $issue= new \IssueWithGetter(1, 'test');
    $this->assertEquals(
      array('issueId' => 1, 'title' => 'test', 'createdAt' => null), 
      $this->fixture->convert($issue)
    );
  }

  /**
   * Test array of value objects
   *
   */
  #[@test]
  public function array_of_issues() {
    $issues= array(
      new IssueWithField(1, 'test1'),
      new IssueWithField(2, 'test2')
    );
    $this->assertEquals(
      array(array('issueId' => 1, 'title' => 'test1'), array('issueId' => 2, 'title' => 'test2')),
      $this->fixture->convert($issues)
    );
  }

  /**
   * Test map of value objects
   *
   */
  #[@test]
  public function map_of_issues() {
    $issues= array(
      'one' => new IssueWithField(1, 'test1'),
      'two' => new IssueWithField(2, 'test2')
    );
    $this->assertEquals(
      array('one' => array('issueId' => 1, 'title' => 'test1'), 'two' => array('issueId' => 2, 'title' => 'test2')),
      $this->fixture->convert($issues)
    );
  }

  /**
   * Test value object
   *
   */
  #[@test]
  public function static_member_excluded() {
    $o= newinstance('lang.Object', array(), '{
      public $name= "Test";
      public static $instance;
    }');
    $this->assertEquals(array('name' => 'Test'), $this->fixture->convert($o));
  }
}
