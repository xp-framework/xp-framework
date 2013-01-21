<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestJsonSerializer',
    'net.xp_framework.unittest.webservices.rest.IssueWithField',
    'net.xp_framework.unittest.webservices.rest.IssueWithGetter',
    'net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreFieldsAndGetter',
    'util.Date',
    'util.TimeZone'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestJsonSerializer
   */
  class RestJsonSerializerTest extends TestCase {
    protected $fixture= NULL;
  
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
        $this->fixture->serialize(new net·xp_framework·unittest·webservices·rest·IssueWithField(1, 'New issue'))
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
        $this->fixture->serialize(new net·xp_framework·unittest·webservices·rest·IssueWithGetter(1, 'New issue'))
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
        $this->fixture->serialize(new net·xp_framework·unittest·webservices·rest·IssueWithGetter(1, 'New issue', new Date('2012-03-19 08:37:00', TimeZone::getByName('GMT'))))
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
        $this->fixture->serialize(new net·xp_framework·unittest·webservices·rest·IssueWithUnderscoreFieldsAndGetter(1, 'New issue'))
      );
    }
  }
?>
