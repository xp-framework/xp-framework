<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'org.codehaus.stomp.frame.SendFrame'
  );

  /**
   * Tests STOMP SendFrame class
   *
   * @see   xp://org.codehaus.stomp.unittest.StompFrameTest
   * @see   xp://org.codehaus.stomp.frame.SendFrame
   */
  class StompSendFrameTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Sets up unittest and creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new org·codehaus·stomp·frame·SendFrame('/queue/test');
    }

    /**
     * Tests setBody()
     *
     */
    #[@test]
    public function setBodySetsContentLengthIfDefined() {
      $this->fixture->addHeader('content-length', 0);
      $this->fixture->setBody('Hello World');
      $this->assertEquals(11, $this->fixture->getHeader('content-length'));
    }

    /**
     * Tests setBody()
     *
     */
    #[@test]
    public function setBodyDoesNotSetContentLengthIfUndefined() {
      $this->fixture->setBody('Hello World');
      $this->assertFalse($this->fixture->hasHeader('content-length'));
    }
  }
?>
