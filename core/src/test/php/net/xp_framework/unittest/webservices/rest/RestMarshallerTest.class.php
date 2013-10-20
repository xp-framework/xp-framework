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
use webservices\rest\RestMarshalling;

/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestMarshalling
 */
class RestMarshallerTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new RestMarshalling();
  }
  
  #[@test]
  public function null() {
    $this->assertEquals(null, $this->fixture->marshal(null));
  }

  #[@test]
  public function string() {
    $this->assertEquals('Hello', $this->fixture->marshal('Hello'));
  }

  #[@test]
  public function string_wrapper_object() {
    $this->assertEquals('Hello', $this->fixture->marshal(new String('Hello')));
  }

  #[@test]
  public function char_wrapper_object() {
    $this->assertEquals('A', $this->fixture->marshal(new \lang\types\Character('A')));
  }

  #[@test]
  public function string_wrapper_object_unicode() {
    $this->assertEquals("\334bercoder", $this->fixture->marshal(new String("\303\234bercoder", 'utf-8')));
  }

  #[@test]
  public function int() {
    $this->assertEquals(6100, $this->fixture->marshal(6100));
  }

  #[@test]
  public function long_wrapper_object() {
    $this->assertEquals(61000, $this->fixture->marshal(new Long(61000)));
  }

  #[@test]
  public function int_wrapper_object() {
    $this->assertEquals(6100, $this->fixture->marshal(new Integer(6100)));
  }

  #[@test]
  public function short_wrapper_object() {
    $this->assertEquals(610, $this->fixture->marshal(new Short(610)));
  }

  #[@test]
  public function byte_wrapper_object() {
    $this->assertEquals(61, $this->fixture->marshal(new Byte(61)));
  }

  #[@test]
  public function double() {
    $this->assertEquals(1.5, $this->fixture->marshal(1.5));
  }

  #[@test]
  public function double_wrapper_object() {
    $this->assertEquals(1.5, $this->fixture->marshal(new Double(1.5)));
  }

  #[@test]
  public function float_wrapper_object() {
    $this->assertEquals(1.5, $this->fixture->marshal(new Float(1.5)));
  }

  #[@test]
  public function bool() {
    $this->assertEquals(true, $this->fixture->marshal(true));
  }

  #[@test]
  public function bool_wrapper_object_true() {
    $this->assertEquals(true, $this->fixture->marshal(\lang\types\Boolean::$TRUE));
  }

  #[@test]
  public function bool_wrapper_object_false() {
    $this->assertEquals(false, $this->fixture->marshal(\lang\types\Boolean::$FALSE));
  }

  #[@test]
  public function string_array() {
    $this->assertEquals(array('Hello', 'World'), $this->fixture->marshal(array('Hello', 'World')));
  }

  #[@test]
  public function string_arraylist() {
    $this->assertEquals(array('Hello', 'World'), $this->fixture->marshal(new ArrayList('Hello', 'World')));
  }

  #[@test]
  public function string_map() {
    $this->assertEquals(
      array('greeting' => 'Hello', 'name' => 'World'),
      $this->fixture->marshal(array('greeting' => 'Hello', 'name' => 'World'))
    );
  }

  #[@test]
  public function date_instance() {
    $this->assertEquals(
      '2012-12-31T18:00:00+01:00',
      $this->fixture->marshal(new \util\Date('2012-12-31 18:00:00', new \util\TimeZone('Europe/Berlin')))
    );
  }

  #[@test]
  public function date_array() {
    $this->assertEquals(
      array('2012-12-31T18:00:00+01:00'),
      $this->fixture->marshal(array(new \util\Date('2012-12-31 18:00:00', new \util\TimeZone('Europe/Berlin'))))
    );
  }

  #[@test]
  public function issue_with_field() {
    $issue= new IssueWithField(1, 'test');
    $this->assertEquals(
      array('issueId' => 1, 'title' => 'test'), 
      $this->fixture->marshal($issue)
    );
  }

  #[@test]
  public function issue_with_getter() {
    $issue= new IssueWithGetter(1, 'test');
    $this->assertEquals(
      array('issueId' => 1, 'title' => 'test', 'createdAt' => null), 
      $this->fixture->marshal($issue)
    );
  }

  #[@test]
  public function array_of_issues() {
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
  public function map_of_issues() {
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
  public function static_member_excluded() {
    $o= newinstance('lang.Object', array(), '{
      public $name= "Test";
      public static $instance;
    }');
    $this->assertEquals(array('name' => 'Test'), $this->fixture->marshal($o));
  }
}
