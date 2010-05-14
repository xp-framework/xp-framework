<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'org.codehaus.stomp.frame.Frame'
  );

  /**
   * Tests STOMP frame class
   *
   * @see   xp://org.codehaus.stomp.unittest.StompSendFrameTest
   * @see   xp://org.codehaus.stomp.frame.Frame
   */
  class StompFrameTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Sets up unittest and creates fixture
     *
     */
    public function setUp() {
      $this->fixture= newinstance('org.codehaus.stomp.frame.Frame', array(), '{
        public function command() { 
          return "test"; 
        }
      }');
    }

    /**
     * Tests getHeaders()
     *
     */
    #[@test]
    public function getHeadersInitiallyEmpty() {
      $this->assertEquals(array(), $this->fixture->getHeaders());
    }

    /**
     * Tests addHeader() and hasHeader()
     *
     */
    #[@test]
    public function hasHeader() {
      $this->fixture->addHeader('content-length', 200);
      $this->assertTrue($this->fixture->hasHeader('content-length'));
    }

    /**
     * Tests addHeader() and getHeader()
     *
     */
    #[@test]
    public function getHeader() {
      $this->fixture->addHeader('content-length', 200);
      $this->assertEquals(200, $this->fixture->getHeader('content-length'));
    }

    /**
     * Tests addHeader() and getHeaders()
     *
     */
    #[@test]
    public function getHeaders() {
      $this->fixture->addHeader('content-length', 200);
      $this->assertEquals(array('content-length' => 200), $this->fixture->getHeaders());
    }
 
    /**
     * Tests requiresImmediateResponse()
     *
     */
    #[@test]
    public function receiptHeader() {
      $this->fixture->addHeader('receipt', 'message-12345');
      $this->assertTrue($this->fixture->requiresImmediateResponse());
    }
  }
?>
