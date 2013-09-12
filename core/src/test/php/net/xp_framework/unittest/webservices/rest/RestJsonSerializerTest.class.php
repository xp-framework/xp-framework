<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestJsonSerializer;
use util\Date;
use util\TimeZone;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestJsonSerializer
 */
class RestJsonSerializerTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new RestJsonSerializer();
  }
  
  /**
   * Test
   *
   */
  #[@test]
  public function emptyArray() {
    $this->assertEquals('[ ]', $this->fixture->serialize(array()));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function intArray() {
    $this->assertEquals('[ 1 , 2 , 3 ]', $this->fixture->serialize(array(1, 2, 3)));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithField() {
    $this->assertEquals(
      '{ "issueId" : 1 , "title" : "New issue" }', 
      $this->fixture->serialize(new IssueWithField(1, 'New issue'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithGetter() {
    $this->assertEquals(
      '{ "issueId" : 1 , "title" : "New issue" , "createdAt" : null }', 
      $this->fixture->serialize(new IssueWithGetter(1, 'New issue'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithGetterAndDate() {
    $this->assertEquals(
      '{ "issueId" : 1 , "title" : "New issue" , "createdAt" : "2012-03-19T08:37:00+00:00" }', 
      $this->fixture->serialize(new IssueWithGetter(1, 'New issue', new Date('2012-03-19 08:37:00', TimeZone::getByName('GMT'))))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function issueWithUnderscoreFieldsAndGetter() {
    $this->assertEquals(
      '{ "issue_id" : 1 , "title" : "New issue" , "created_at" : null }', 
      $this->fixture->serialize(new IssueWithUnderscoreFieldsAndGetter(1, 'New issue'))
    );
  }
}
