<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestDataCaster'
  );
  
  /**
   * Test data casting
   *
   */
  class RestDataCasterTest extends TestCase {
    
    /**
     * Test simplify primitives
     * 
     */
    #[@test]
    public function simplifyPrimitives() {
      $this->assertEquals(1, RestDataCaster::simple(1));
      $this->assertEquals('test', RestDataCaster::simple('test'));
      $this->assertTrue(RestDataCaster::simple(TRUE));
    }
    
    /**
     * Test simplify array
     * 
     */
    #[@test]
    public function simplifyArray() {
      $this->assertEquals(array(1, 2, 3), RestDataCaster::simple(array(1, 2, 3)));
    }
    
    /**
     * Test simplify hashmap
     * 
     */
    #[@test]
    public function simplifyHashmap() {
      $this->assertEquals(array('one' => 1, 'two' => 2, 'three' => 3), RestDataCaster::simple(new Hashmap(array('one' => 1, 'two' => 2, 'three' => 3))));
    }
    
    /**
     * Test simplify stdclass
     * 
     */
    #[@test]
    public function simplifyStdclass() {
      $inst= new stdClass();
      $inst->one= 1;
      $inst->two= 2;
      $inst->three= 3;
      
      $this->assertEquals(array('one' => 1, 'two' => 2, 'three' => 3), RestDataCaster::simple($inst));
    }
    
    /**
     * Test simplify Object with public fields
     * 
     */
    #[@test]
    public function simplifyObjectWithPublicFields() {
      $inst= newinstance('lang.Object', array(), '{
        public $one= 1;
        public $two= 2;
        public $three= 3;
      }');
      
      $this->assertEquals(array('one' => 1, 'two' => 2, 'three' => 3), RestDataCaster::simple($inst));
    }
    
    /**
     * Test simplify Object with private fields but accessor functions
     * 
     */
    #[@test]
    public function simplifyObjectWithPrivateFieldsButPublicMethods() {
      $inst= newinstance('lang.Object', array(), '{
        protected $one= 1;
        protected $two= 2;
        protected $three= 3;
        
        public function getOne() { return $this->one; }
        public function getTwo() { return $this->two; }
        public function getThree() { return $this->three; }
      }');
      
      $this->assertEquals(array('one' => 1, 'two' => 2, 'three' => 3), RestDataCaster::simple($inst));
    }
    
    /**
     * Test complexify Object with private fields but accessor functions
     * 
     */
    #[@testx]
    public function complexifyObjectWithPrivateFieldsButPublicMethods() {
      $casted= RestDataCaster::simple(self::$objThreePublicMethods);
      
      $this->assertEquals(1, $casted->getOne());
      $this->assertEquals(2, $casted->getTwo());
      $this->assertEquals(3, $casted->getThree());
    }
  }
?>