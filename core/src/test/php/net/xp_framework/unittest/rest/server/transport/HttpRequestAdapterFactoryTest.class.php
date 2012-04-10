<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest',
    'webservices.rest.server.transport.HttpRequestAdapterFactory'
  );
  
  /**
   * Test HTTP request adapter factory
   *
   */
  class HttpRequestAdapterFactoryTest extends TestCase {
    protected $request= NULL;
    
    /**
     * Set up
     * 
     */
    public function setUp() {
      $this->request= new HttpScriptletRequest();
    }
    
    /**
     * Test wrong type
     * 
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function wrongContentType() {
      $this->request->addHeader('Content-Type', 'something/wrong');
      
      HttpRequestAdapterFactory::forRequest($this->request);
    }
    
    /**
     * Test JSON content type
     * 
     */
    #[@test]
    public function jsonContentType() {
      $this->request->addHeader('Content-Type', 'application/json');
      
      $this->assertEquals('webservices.rest.server.transport.JsonHttpRequestAdapter', HttpRequestAdapterFactory::forRequest($this->request)->getName());
    }
  }
?>
