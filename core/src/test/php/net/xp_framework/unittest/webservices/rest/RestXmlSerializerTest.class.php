<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestXmlSerializer;
use util\Date;
use util\TimeZone;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestXmlSerializer
 */
class RestXmlSerializerTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new RestXmlSerializer();
  }
  
  /**
   * Asserttion helper
   *
   * @param   string expected
   * @param   string actual
   * @throws  unittest.AssertionFailedError
   */
  protected function assertXmlEquals($expected, $actual) {
    $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>'."\n".$expected, $actual);
  }
  
  /**
   * Test
   *
   */
  #[@test]
  public function emptyArray() {
    $this->assertXmlEquals(
      '<root></root>', 
      $this->fixture->serialize(array())
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function intArray() {
    $this->assertXmlEquals(
      '<root><root>1</root><root>2</root><root>3</root></root>', 
      $this->fixture->serialize(array(1, 2, 3))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithField() {
    $this->assertXmlEquals(
      '<root><issueId>1</issueId><title>New issue</title></root>', 
      $this->fixture->serialize(new IssueWithField(1, 'New issue'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithGetter() {
    $this->assertXmlEquals(
      '<root><issueId>1</issueId><title>New issue</title><createdAt></createdAt></root>', 
      $this->fixture->serialize(new IssueWithGetter(1, 'New issue'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithGetterAndDate() {
    $this->assertXmlEquals(
      '<root><issueId>1</issueId><title>New issue</title><createdAt>2012-03-19T08:37:00+00:00</createdAt></root>', 
      $this->fixture->serialize(new IssueWithGetter(1, 'New issue', new Date('2012-03-19 08:37:00', TimeZone::getByName('GMT'))))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithUnderscoreFieldsAndGetter() {
    $this->assertXmlEquals(
      '<root><issue_id>1</issue_id><title>New issue</title><created_at></created_at></root>', 
      $this->fixture->serialize(new IssueWithUnderscoreFieldsAndGetter(1, 'New issue'))
    );
  }
}
