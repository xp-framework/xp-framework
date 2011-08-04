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
    
    /**
     * Test binding with non-existant key
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function bindWrongkey() {
      $this->fixture->getBinding('unknown');
    }
    
    /**
     * Test binding with key using array
     * 
     */
    #[@test]
    public function bindArrayKey() {
      $this->fixture->bind('test', array('first' => 1, 'second' => 2));
      
      $this->assertEquals(1, $this->fixture->getBinding('test[first]'));
    }
    
    /**
     * Test binding with wrong key using array
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function bindArrayWrongKey() {
      $this->fixture->bind('test', array('first' => 1, 'second' => 2));
      $this->fixture->getBinding('test[wrong]');
    }
    
    /**
     * Test binding with key using object
     * 
     */
    #[@test]
    public function bindObjectKey() {
      $this->fixture->bind('test', newinstance('lang.Object', array(), '{
        public $first= 1;
      }'));
      $this->fixture->getBinding('test[first]');
    }
    
    /**
     * Test invalid binding name
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function bindInvalid() {
      $this->fixture->bind('test', array('first' => 1, 'second' => 2));
      $this->fixture->getBinding('test[missingbracket');
    }
  }
?>
