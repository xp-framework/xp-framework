<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.String',
    'lang.types.Long',
    'lang.types.Integer',
    'lang.types.Short',
    'lang.types.Byte',
    'lang.types.Double',
    'lang.types.Float',
    'lang.types.ArrayList',
    'webservices.rest.RestMarshalling',
    'net.xp_framework.unittest.webservices.rest.ConstructorFixture',
    'net.xp_framework.unittest.webservices.rest.IssueWithField',
    'net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreField',
    'net.xp_framework.unittest.webservices.rest.IssueWithSetter',
    'net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreSetter'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestMarshalling
   */
  class RestMarshallerTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new RestMarshalling();
    }
    
    /**
     * Test null
     *
     */
    #[@test]
    public function null() {
      $this->assertEquals(NULL, $this->fixture->marshal(NULL));
    }

    /**
     * Test a string
     *
     */
    #[@test]
    public function string() {
      $this->assertEquals('Hello', $this->fixture->marshal('Hello'));
    }

    /**
     * Test a string
     *
     */
    #[@test]
    public function string_wrapper_object() {
      $this->assertEquals('Hello', $this->fixture->marshal(new String('Hello')));
    }

    /**
     * Test a string
     *
     */
    #[@test]
    public function char_wrapper_object() {
      $this->assertEquals('A', $this->fixture->marshal(new Character('A')));
    }

    /**
     * Test a string
     *
     */
    #[@test]
    public function string_wrapper_object_unicode() {
      $this->assertEquals("\334bercoder", $this->fixture->marshal(new String("\303\234bercoder", 'utf-8')));
    }

    /**
     * Test an integer
     *
     */
    #[@test]
    public function int() {
      $this->assertEquals(6100, $this->fixture->marshal(6100));
    }

    /**
     * Test an integer
     *
     */
    #[@test]
    public function long_wrapper_object() {
      $this->assertEquals(61000, $this->fixture->marshal(new Long(61000)));
    }

    /**
     * Test an integer
     *
     */
    #[@test]
    public function int_wrapper_object() {
      $this->assertEquals(6100, $this->fixture->marshal(new Integer(6100)));
    }

    /**
     * Test a short
     *
     */
    #[@test]
    public function short_wrapper_object() {
      $this->assertEquals(610, $this->fixture->marshal(new Short(610)));
    }

    /**
     * Test a byte
     *
     */
    #[@test]
    public function byte_wrapper_object() {
      $this->assertEquals(61, $this->fixture->marshal(new Byte(61)));
    }

    /**
     * Test a double
     *
     */
    #[@test]
    public function double() {
      $this->assertEquals(1.5, $this->fixture->marshal(1.5));
    }

    /**
     * Test a double
     *
     */
    #[@test]
    public function double_wrapper_object() {
      $this->assertEquals(1.5, $this->fixture->marshal(new Double(1.5)));
    }

    /**
     * Test a float
     *
     */
    #[@test]
    public function float_wrapper_object() {
      $this->assertEquals(1.5, $this->fixture->marshal(new Float(1.5)));
    }

    /**
     * Test a boolean
     *
     */
    #[@test]
    public function bool() {
      $this->assertEquals(TRUE, $this->fixture->marshal(TRUE));
    }

    /**
     * Test a boolean
     *
     */
    #[@test]
    public function bool_wrapper_object_true() {
      $this->assertEquals(TRUE, $this->fixture->marshal(Boolean::$TRUE));
    }

    /**
     * Test a boolean
     *
     */
    #[@test]
    public function bool_wrapper_object_false() {
      $this->assertEquals(FALSE, $this->fixture->marshal(Boolean::$FALSE));
    }

    /**
     * Test an array of strings
     *
     */
    #[@test]
    public function string_array() {
      $this->assertEquals(array('Hello', 'World'), $this->fixture->marshal(array('Hello', 'World')));
    }

    /**
     * Test an array of strings
     *
     */
    #[@test]
    public function string_arraylist() {
      $this->assertEquals(array('Hello', 'World'), $this->fixture->marshal(new ArrayList('Hello', 'World')));
    }

    /**
     * Test an array of strings
     *
     */
    #[@test]
    public function string_map() {
      $this->assertEquals(
        array('greeting' => 'Hello', 'name' => 'World'),
        $this->fixture->marshal(array('greeting' => 'Hello', 'name' => 'World'))
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
        $this->fixture->marshal(new Date('2012-12-31 18:00:00', new TimeZone('Europe/Berlin')))
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
        $this->fixture->marshal(array(new Date('2012-12-31 18:00:00', new TimeZone('Europe/Berlin'))))
      );
    }

    /**
     * Test value object
     *
     */
    #[@test]
    public function issue_with_field() {
      $issue= new net·xp_framework·unittest·webservices·rest·IssueWithField(1, 'test');
      $this->assertEquals(
        array('issueId' => 1, 'title' => 'test'), 
        $this->fixture->marshal($issue)
      );
    }

    /**
     * Test value object
     *
     */
    #[@test]
    public function issue_with_getter() {
      $issue= new net·xp_framework·unittest·webservices·rest·IssueWithGetter(1, 'test');
      $this->assertEquals(
        array('issueId' => 1, 'title' => 'test', 'createdAt' => NULL), 
        $this->fixture->marshal($issue)
      );
    }

    /**
     * Test array of value objects
     *
     */
    #[@test]
    public function array_of_issues() {
      $issues= array(
        new net·xp_framework·unittest·webservices·rest·IssueWithField(1, 'test1'),
        new net·xp_framework·unittest·webservices·rest·IssueWithField(2, 'test2')
      );
      $this->assertEquals(
        array(array('issueId' => 1, 'title' => 'test1'), array('issueId' => 2, 'title' => 'test2')),
        $this->fixture->marshal($issues)
      );
    }

    /**
     * Test map of value objects
     *
     */
    #[@test]
    public function map_of_issues() {
      $issues= array(
        'one' => new net·xp_framework·unittest·webservices·rest·IssueWithField(1, 'test1'),
        'two' => new net·xp_framework·unittest·webservices·rest·IssueWithField(2, 'test2')
      );
      $this->assertEquals(
        array('one' => array('issueId' => 1, 'title' => 'test1'), 'two' => array('issueId' => 2, 'title' => 'test2')),
        $this->fixture->marshal($issues)
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
      $this->assertEquals(array('name' => 'Test'), $this->fixture->marshal($o));
    }
  }
?>
