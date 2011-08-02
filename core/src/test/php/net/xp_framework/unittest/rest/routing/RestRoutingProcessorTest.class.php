<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.routing.RestRoutingProcessor'
  );
  
  /**
   * Test routing processor
   *
   */
  class RestRoutingProcessorTest extends TestCase {
    protected
      $fixture= NULL;
    
    /**
     * Set up
     * 
     */
    public function setUp() {
      $this->fixture= new RestRoutingProcessor();
    }

    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.routing.RestRoutingProcessor', $this->fixture);
    }
    
    /**
     * Test binding
     * 
     */
    #[@test]
    public function bind() {
      $this->fixture->bind('test', $obj= new Object());
      
      $this->assertEquals($obj, $this->fixture->getBinding('test'));
    }
  }
?>
