<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest',
    'webservices.rest.transport.JsonHttpRequestAdapter'
  );
  
  /**
   * Test JSON HTTP request adapter class
   *
   */
  class JsonHttpRequestAdapterTest extends TestCase {
    protected $request= NULL;
    protected $fixture= NULL;
    
    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->request= new HttpScriptletRequest();
      $this->fixture= new JsonHttpRequestAdapter($this->request);
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.transport.JsonHttpRequestAdapter', $this->fixture);
    }
    
    /**
     * Test getHeader() with header not set
     * 
     */
    #[@test]
    public function headerNotSet() {
      $this->assertNull($this->fixture->getHeader('Test'));
    }
    
    /**
     * Test getHeader() with header set
     * 
     */
    #[@test]
    public function headerSet() {
      $this->request->addHeader('Test', 'Test value');
      $this->assertEquals('Test value', $this->fixture->getHeader('Test'));
    }
    
    /**
     * Test getPath()
     * 
     */
    #[@test]
    public function getPath() {
      $this->request->setURL(new HttpScriptletURL('http://localhost/some/path'));
      $this->assertEquals('/some/path', $this->fixture->getPath());
    }
    
    /**
     * Test getParam() not set
     * 
     */
    #[@test]
    public function paramNotSet() {
      $this->assertNull($this->fixture->getParam('test'));
    }
    
    /**
     * Test getParam() with parameter set
     *  
     */
    #[@test]
    public function paramSet() {
      $this->request->setParam('test', 'test value');
      $this->assertEquals('test value', $this->fixture->getParam('test'));
    }
    
    /**
     * Test getData()
     * 
     */
    #[@test]
    public function getData() {
      $this->request->setData('{ "some" : "thing" }');
      $this->assertEquals(array('some' => 'thing'), $this->fixture->getData());
    }
  }
?>
