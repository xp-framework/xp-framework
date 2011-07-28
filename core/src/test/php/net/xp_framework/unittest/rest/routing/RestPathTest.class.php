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
     * Test simple parameter
     * 
     */
    #[@test]
    public function simpleParam() {
      $path= new RestPath('/path/to/item/{id}');
      
      $this->assertTrue($path->match('/path/to/item/1234'));
      $this->assertEquals('1234', $path->getPathParam('id'));
    }
    
    /**
     * Test multiple parameters
     * 
     */
    #[@test]
    public function multipleParams() {
      $path= new RestPath('/path/to/item/{id}/entity/{entity}');
      
      $this->assertTrue($path->match('/path/to/item/1234/entity/1337'));
      $this->assertEquals('1234', $path->getPathParam('id'));
      $this->assertEquals('1337', $path->getPathParam('entity'));
    }
  }
?>
