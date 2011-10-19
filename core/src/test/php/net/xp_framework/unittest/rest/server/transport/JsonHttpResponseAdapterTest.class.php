<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletResponse',
    'webservices.rest.server.transport.JsonHttpResponseAdapter'
  );
  
  /**
   * Test JSON HTTP response adapter class
   *
   */
  class JsonHttpResponseAdapterTest extends TestCase {
    protected $response= NULL;
    protected $fixture= NULL;
    
    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->response= new HttpScriptletResponse();
      $this->fixture= new JsonHttpResponseAdapter($this->response);
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.server.transport.JsonHttpResponseAdapter', $this->fixture);
    }
    
    /**
     * Test setStatus()
     * 
     */
    #[@test]
    public function setStatus() {
      $this->fixture->setStatus(HttpConstants::STATUS_OK);
      $this->assertEquals(HttpConstants::STATUS_OK, $this->response->statusCode);
    }
    
    /**
     * Test setHeader() with header not set
     * 
     */
    #[@test]
    public function header() {
      $this->fixture->setHeader('Test', 'Test value');
      $this->assertEquals('Test value', $this->response->getHeader('Test'));
    }
    
    /**
     * Test getData()
     * 
     */
    #[@xtest]
    public function getData() {
      $this->request->setData('{ "some" : "thing" }');
      $this->assertEquals(array('some' => 'thing'), $this->fixture->getData());
    }
  }
?>
