<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest',
    'webservices.rest.server.transport.HttpResponseAdapterFactory'
  );
  
  /**
   * Test HTTP response adapter factory
   *
   */
  class HttpResponseAdapterFactoryTest extends TestCase {
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
      
      HttpResponseAdapterFactory::forRequest($this->request);
    }
    
    /**
     * Test JSON content type
     * 
     */
    #[@test]
    public function jsonContentType() {
      $this->request->addHeader('Accept', 'application/json');
      
      $this->assertEquals('webservices.rest.server.transport.JsonHttpResponseAdapter', HttpResponseAdapterFactory::forRequest($this->request)->getName());
    }
  }
?>
