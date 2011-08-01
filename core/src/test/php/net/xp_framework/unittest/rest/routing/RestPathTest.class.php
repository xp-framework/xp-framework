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
     * Test getParamNames()
     * 
     */
    #[@test]
    public function getParamNames() {
      $this->assertEquals(array('id'), create(new RestPath('/path/to/{id}'))->getParamNames());
    }
    
    /**
     * Test hasParam()
     * 
     */
    #[@test]
    public function hasParam() {
      $this->assertFalse(create(new RestPath('/'))->hasParam('id'));
    }
    
    /**
     * Test hasParam() when parameter exist
     * 
     */
    #[@test]
    public function hasParamWithParameterSet() {
      $this->assertTrue(create(new RestPath('/{id}'))->hasParam('id'));
    }
    
    /**
     * Test simple parameter
     * 
     */
    #[@test]
    public function simpleParam() {
      $this->assertEquals(array(
        'id' => '1234'
      ),
        create(new RestPath('/path/to/item/{id}'))->match('/path/to/item/1234')
      );
    }
    
    /**
     * Test multiple parameters
     * 
     */
    #[@test]
    public function multipleParams() {
      $this->assertEquals(array(
        'id' => '1234',
        'entity' => '1337'
      ),
        create(new RestPath('/path/to/item/{id}/entity/{entity}'))->match('/path/to/item/1234/entity/1337')
      );
    }
  }
?>
