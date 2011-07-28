<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.routing.RestAnnotationRouter'
  );
  
  /**
   * Test annotation based router
   *
   */
  class RestAnnotationRouterTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new RestAnnotationRouter();
      $this->fixture->configure($this->getClass()->getPackage()->getName().'.mock');
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.routing.RestRouter', $this->fixture);
    }
    
    /**
     * Test configuration
     * 
     */
    #[@test]
    public function configure() {
      $this->fixture->configure($this->getClass()->getPackage()->getName().'.mock');
    }
    
    /**
     * Test hasRoutesFor()
     * 
     */
    #[@test]
    public function hasRoutesFor() {
      $this->assertFalse($this->fixture->hasRoutesFor('PUT', '/'));
    }
    
    /**
     * Test hasRoutesFor()
     * 
     */
    #[@test]
    public function hasRoutesForPath() {
      $this->assertTrue($this->fixture->hasRoutesFor('GET', '/some/thing'));
    }
    
    /**
     * Test hasRoutesFor() with case insensitive method
     * 
     */
    #[@test]
    public function hasRoutesForPathCaseInsensitive() {
      $this->assertTrue($this->fixture->hasRoutesFor('get', '/some/thing'));
    }
  }
?>
