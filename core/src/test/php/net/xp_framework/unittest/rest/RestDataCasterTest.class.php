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
    protected static
      $arrayThree= NULL,
      $arrayThreeHash= NULL,
      $arrayTwoHash= NULL,
      $stdClassThree= NULL,
      $objThreeFields= NULL,
      $objThreeMethods= NULL;
    
    /**
     * Before class setup
     * 
     */
    #[@beforeClass]
    public static function beforeClass() {
      self::$arrayThree= array('1', '2', '3');
      self::$arrayThreeHash= array('one' => '1', 'two' => '2', 'three' => '3');
      self::$arrayTwoHash= array('one' => '1', 'two' => '2');
      
      self::$stdClassThree= new stdClass();
      self::$stdClassThree->one= '1';
      self::$stdClassThree->two= '2';
      self::$stdClassThree->three= '3';
      
      self::$objThreeFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
        public $three= "3";
      }');
      self::$objThreeMethods= newinstance('lang.Object', array(), '{
        protected $one= "1";
        protected $two= "2";
        protected $three= "3";
        
        public function getOne() { return $this->one; }
        public function getTwo() { return $this->two; }
        public function getThree() { return $this->three; }
      }');
    }
    
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
      $this->assertEquals(self::$arrayThree, RestDataCaster::simple(self::$arrayThree));
    }
    
    /**
     * Test simplify hashmap
     * 
     */
    #[@test]
    public function simplifyHashmap() {
      $this->assertEquals(self::$arrayThreeHash, RestDataCaster::simple(new Hashmap(self::$arrayThreeHash)));
    }
    
    /**
     * Test simplify stdclass
     * 
     */
    #[@test]
    public function simplifyStdclass() {
      $this->assertEquals(self::$arrayThreeHash, RestDataCaster::simple(self::$stdClassThree));
    }
    
    /**
     * Test simplify Object with public fields
     * 
     */
    #[@test]
    public function simplifyObjectWithPublicFields() {
      $this->assertEquals(self::$arrayThreeHash, RestDataCaster::simple(self::$objThreeFields));
    }
    
    /**
     * Test simplify Object with private fields but accessor functions
     * 
     */
    #[@test]
    public function simplifyObjectWithPrivateFieldsButPublicMethods() {
      $this->assertEquals(self::$arrayThreeHash, RestDataCaster::simple(self::$objThreeMethods));
    }
    
    /**
     * Test simplify primitives
     * 
     */
    #[@test]
    public function complexifyPrimitives() {
      $this->assertEquals(1, RestDataCaster::complex(1, XPClass::forName('lang.types.Integer')));
      $this->assertEquals('test', RestDataCaster::complex('test', XPClass::forName('lang.types.String')));
      $this->assertTrue(RestDataCaster::simple(TRUE, XPClass::forName('lang.types.Boolean')));
    }
    
    /**
     * Test simplify array
     * 
     */
    #[@test]
    public function complexifyArray() {
      $this->assertEquals(self::$arrayThree, RestDataCaster::complex(self::$arrayThree, XPClass::forName('lang.types.ArrayList')));
    }
    
    /**
     * Test simplify hashmap
     * 
     */
    #[@test]
    public function complexifyHashmap() {
      $this->assertEquals(new Hashmap(self::$arrayThreeHash), RestDataCaster::complex(self::$arrayThreeHash, XPClass::forName('util.Hashmap')));
    }
    
    /**
     * Test simplify stdclass
     * 
     */
    #[@test]
    public function complexifyStdclass() {
      $casted= RestDataCaster::complex(self::$arrayThreeHash, new Type('php.stdClass'));
      
      $this->assertEquals((array)self::$stdClassThree, (array)$casted);
    }
    
    /**
     * Test simplify Object with public fields
     * 
     */
    #[@test]
    public function complexifyObjectWithPublicFields() {
      $casted= RestDataCaster::complex(self::$arrayThreeHash, self::$objThreeFields->getClass());
      
      $this->assertEquals('1', $casted->one);
      $this->assertEquals('2', $casted->two);
      $this->assertEquals('3', $casted->three);
    }
    
    /**
     * Test complexify Object with private fields but accessor functions
     * 
     */
    #[@testx]
    public function complexifyObjectWithPrivateFieldsButPublicMethods() {
      $casted= RestDataCaster::complex(self::$arrayThreeHash, self::$objThreePublicMethods->getClass());
      
      $this->assertEquals('1', $casted->getOne());
      $this->assertEquals('2', $casted->getTwo());
      $this->assertEquals('3', $casted->getThree());
    }
  }
?>