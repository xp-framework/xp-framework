<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestSerializer',
    'net.xp_framework.unittest.webservices.rest.ConstructorFixture',
    'net.xp_framework.unittest.webservices.rest.IssueWithField',
    'net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreField',
    'net.xp_framework.unittest.webservices.rest.IssueWithSetter',
    'net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreSetter'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestDeserializer
   */
  class RestSerializerConversionTest extends TestCase {
    protected $fixture= NULL;
  
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
      $this->assertEquals(NULL, $this->fixture->convert(NULL));
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
     * Test an integer
     *
     */
    #[@test]
    public function int() {
      $this->assertEquals(6100, $this->fixture->convert(6100));
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
     * Test a boolean
     *
     */
    #[@test]
    public function bool() {
      $this->assertEquals(TRUE, $this->fixture->convert(TRUE));
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
        'Mon, 31 Dec 2012 18:00:00 +0100',
        $this->fixture->convert(new Date('2012-12-31 18:00:00', new TimeZone('Europe/Berlin')))
      );
    }

    /**
     * Test a date instance
     *
     */
    #[@test]
    public function date_array() {
      $this->assertEquals(
        array('Mon, 31 Dec 2012 18:00:00 +0100'),
        $this->fixture->convert(array(new Date('2012-12-31 18:00:00', new TimeZone('Europe/Berlin'))))
      );
    }
  }
?>
