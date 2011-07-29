<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest',
    'webservices.rest.routing.RestPath'
  );
  
  /**
   * Test REST resource path 
   *
   */
  class RestPathTest extends TestCase {
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.routing.RestPath', new RestPath('/'));
    }
    
    /**
     * Test getPath()
     * 
     */
    #[@test]
    public function getPath() {
      $this->assertEquals('/path/to/{id}/details', create(new RestPath('/path/to/{id}/details'))->getPath());
    }
    
    /**
     * Test setParam()/getParam() when no parameter is set
     * 
     */
    #[@test]
    public function paramNotSet() {
      $this->assertNull(create(new RestPath('/path/{id}'))->getParam('id'));
    }
    
    /**
     * Test setParam()/getParam() with setting non-existant
     * parameter
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function paramSetNonexitant() {
      create(new RestPath('/'))->setParam('unknown', 123);
    }
    
    /**
     * Test setParam()/getParam() with parameter set
     * 
     */
    #[@test]
    public function paramWithParameter() {
      $path= new RestPath('/{id}');
      $path->setParam('id', 123);
      
      $this->assertEquals(123, $path->getParam('id'));
    }
    
    /**
     * Test simple parameter
     * 
     */
    #[@test]
    public function simpleParam() {
      $path= new RestPath('/path/to/item/{id}');
      
      $this->assertTrue($path->match('/path/to/item/1234'));
      $this->assertEquals('1234', $path->getParam('id'));
    }
    
    /**
     * Test multiple parameters
     * 
     */
    #[@test]
    public function multipleParams() {
      $path= new RestPath('/path/to/item/{id}/entity/{entity}');
      
      $this->assertTrue($path->match('/path/to/item/1234/entity/1337'));
      $this->assertEquals('1234', $path->getParam('id'));
      $this->assertEquals('1337', $path->getParam('entity'));
    }
  }
?>
