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
     * Test query string parsing
     * 
     */
    #[@test]
    public function getParamNamesForQuery() {
      $this->assertTrue(create(new RestPath('/path/?p1={p1}'))->hasParam('p1'));
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
    
    /**
     * Test simple query parameter
     * 
     */
    #[@test]
    public function simpleQueryParam() {
      $this->assertEquals(array(
        'p1' => '1234'
      ),
        create(new RestPath('/path/to/item/?id={p1}'))->match('/path/to/item/?id=1234')
      );
    }
    
    /**
     * Test multiple parameters
     * 
     */
    #[@test]
    public function multipleQueryParams() {
      $this->assertEquals(array(
        'p1' => '1234',
        'p2' => '5678'
      ),
        create(new RestPath('/path/to/item/?id={p1}&key={p2}'))->match('/path/to/item/?key=5678&id=1234')
      );
    }
  }
?>
