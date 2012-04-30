<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest'
  );
  
  /**
   * Abstact test for HTTP request adapter classes
   *
   */
  abstract class AbstractHttpRequestAdapterTest extends TestCase {
    protected $request= NULL;
    protected $fixture= NULL;
    
    /**
     * Return adapter class name
     *
     * @return string
     */
    abstract protected function adapter();
    
    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->request= new HttpScriptletRequest();
      $this->fixture= XPClass::forName($this->adapter())->newInstance($this->request);
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf($this->adapter(), $this->fixture);
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
     * Test getQueryString()
     *  
     */
    #[@test]
    public function getQueryString() {
      $this->request->setParam('product1', 'name&name');
      $this->request->setParam('product2', 'test&test');
      $this->assertEquals('product1=name%26name&product2=test%26test', $this->fixture->getQueryString());
    }
    
    /**
     * Test getQueryString()
     *  
     */
    #[@test]
    public function getQueryStringEqualWithHttpRequestQueryString() {
      $this->request->env['QUERY_STRING']='product1=name%26name&product2=test%26test';
      $this->request->setParam('product1', 'name&name');
      $this->request->setParam('product2', 'test&test');
      $this->assertNotEquals($this->request->getQueryString(), $this->fixture->getQueryString());
      $this->assertEquals($this->request->getQueryString(), urldecode($this->fixture->getQueryString()));
    }
  }
?>
