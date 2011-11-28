<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.server.RestDataCaster'
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
      $objPrivateFields= NULL,
      $objThreeFields= NULL,
      $objThreeTypedFields= NULL,
      $objThreeMethods= NULL,
      $objTwoFields= NULL;
    
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
      
      self::$objPrivateFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
        protected $three= "3";
      }');
      self::$objTwoFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
      }');
      self::$objThreeFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
        public $three= "3";
        public function equals($o) { return $this->one == $o->one && $this->two == $o->two && $this->three == $o->three; }
      }');
      self::$objThreeTypedFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
        #[@type(\''.self::$objTwoFields->getClassName().'\')]
        public $three= "3";
      }');
      self::$objThreeMethods= newinstance('lang.Object', array(), '{
        protected $one= "1";
        protected $two= "2";
        protected $three= "3";
        
        public function getOne() { return $this->one; }
        public function setOne($v) { $this->one= $v; }
        public function getTwo() { return $this->two; }
        public function setTwo($v) { $this->two= $v; }
        public function getThree() { return $this->three; }
        public function setThree($v) { $this->three= $v; }
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
     * Test simplify primitives object
     * 
     */
    #[@test]
    public function simplifyPrimitivesObject() {
      $this->assertEquals(1, RestDataCaster::simple(new Integer(1)));
      $this->assertEquals('test', RestDataCaster::simple(new String('test')));
      $this->assertEquals(TRUE, RestDataCaster::simple(new Boolean(TRUE)));
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
     * Test simplify array type
     *
     */
    #[@test]
    public function simplifyArrayType() {
      $this->assertEquals(array(
        self::$arrayThreeHash, self::$arrayThreeHash
      ), RestDataCaster::simple(
        array(self::$objThreeFields, self::$objThreeFields)
      ));
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
     * Test simplify Object with Object inside
     * 
     */
    #[@test]
    public function simplifyObjectWithObjectInside() {
      $instance= clone self::$objThreeFields;
      $instance->three= self::$objTwoFields;
      
      $result= self::$arrayThreeHash;
      $result['three']= self::$arrayTwoHash;
      
      $this->assertEquals($result, RestDataCaster::simple($instance));
    }
    
    /**
     * Test simplify Object ignoring private fields
     * 
     */
    #[@test]
    public function simplifyObjectIgnoringPrivateFields() {
      $this->assertEquals(self::$arrayTwoHash, RestDataCaster::simple(self::$objPrivateFields));
    }
    
    /**
     * Test complexify primitives
     * 
     */
    #[@test]
    public function complexifyPrimitives() {
      $this->assertEquals(1, RestDataCaster::complex(1, XPClass::forName('lang.types.Integer')));
      $this->assertEquals('test', RestDataCaster::complex('test', XPClass::forName('lang.types.String')));
      $this->assertTrue(RestDataCaster::simple(TRUE, XPClass::forName('lang.types.Boolean')));
    }
    
    /**
     * Test complexify primitives object
     * 
     */
    #[@test]
    public function complexifyPrimitivesObject() {
      $this->assertEquals(1, RestDataCaster::complex(1, Primitive::$INT));
      $this->assertEquals('test', RestDataCaster::complex('test', Primitive::$STRING));
      $this->assertEquals(TRUE, RestDataCaster::complex(TRUE, Primitive::$BOOLEAN));
    }
    
    /**
     * Test complexify wrong data types to primitive type
     * 
     */
    #[@test]
    public function complexifyToPrimitiveTypesFails() {
      $casts= array(
        array(Primitive::$INT, array()),
        array(Primitive::$INT, new Object()),
        array(Primitive::$INT, '1.3'),
        array(Primitive::$INT, 'test'),
        
        array(Primitive::$BOOLEAN, array()),
        array(Primitive::$BOOLEAN, new Object()),
        
        array(Primitive::$STRING, array()),
        array(Primitive::$STRING, new Object()),
      );
      
      foreach ($casts as $cast) {
        try {
          RestDataCaster::complex($cast[1], $cast[0]);
          
          throw new IllegalStateException('Cast '.xp::typeOf($cast[1]).' to '.$cast[0]->getName().' should throw exception');
        } catch (ClassCastException $e) {
          // Ignore this, because it's assumed and OK
        }
      }
    }
    
    /**
     * Test complexify wrong data types to lang.Object
     * 
     */
    #[@test]
    public function complexifyToObjectFails() {
      $casts= array(1, TRUE, 'test');
      
      foreach ($casts as $cast) {
        try {
          RestDataCaster::complex($cast, XPClass::forName('lang.Object'));
          
          throw new IllegalStateException('Cast '.xp::typeOf($cast).' to lang.Object should throw exception');
        } catch (ClassCastException $e) {
          // Ignore this, because it's assumed and OK
        }
      }
    }
    
    /**
     * Test complexify array
     * 
     */
    #[@test]
    public function complexifyArray() {
      $this->assertEquals(self::$arrayThree, RestDataCaster::complex(self::$arrayThree, XPClass::forName('lang.types.ArrayList')));
    }

    /**
     * Test complexify array type
     *
     */
    #[@test]
    public function complexifyArrayType() {
      $this->assertEquals(array(
        self::$objThreeFields, self::$objThreeFields
      ), RestDataCaster::complex(
        array((array)self::$objThreeFields, (array)self::$objThreeFields),
        Type::forName(self::$objThreeFields->getClassName().'[]')
      ));
    }
    
    /**
     * Test complexify hashmap
     * 
     */
    #[@test]
    public function complexifyHashmap() {
      $this->assertEquals(new Hashmap(self::$arrayThreeHash), RestDataCaster::complex(self::$arrayThreeHash, XPClass::forName('util.Hashmap')));
    }
    
    /**
     * Test complexify stdclass
     * 
     */
    #[@test]
    public function complexifyStdclass() {
      $casted= RestDataCaster::complex(self::$arrayThreeHash, new Type('php.stdClass'));
      
      $this->assertEquals((array)self::$stdClassThree, (array)$casted);
    }
    
    /**
     * Test complexify Object with public fields
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
    #[@test]
    public function complexifyObjectWithPrivateFieldsButPublicMethods() {
      $casted= RestDataCaster::complex(self::$arrayThreeHash, self::$objThreeMethods->getClass());
      
      $this->assertEquals('1', $casted->getOne());
      $this->assertEquals('2', $casted->getTwo());
      $this->assertEquals('3', $casted->getThree());
    }
    
    /**
     * Test complexify array with array inside
     * 
     */
    #[@test]
    public function complexifyObjectWithObjectInside() {
      $instance= clone self::$objThreeFields;
      $instance->three= self::$objTwoFields;
      
      $result= self::$arrayThreeHash;
      $result['three']= self::$arrayTwoHash;
      
      $casted= RestDataCaster::complex($result, self::$objThreeTypedFields->getClass());
      
      $this->assertInstanceOf(self::$objThreeTypedFields->getClassName(), $casted);
      $this->assertInstanceOf(self::$objTwoFields->getClassName(), $casted->three);
    }
    
    /**
     * Test complexify array list with wrong data
     * 
     */
    #[@test, @expect('lang.ClassCastException')]
    public function complexifyArrayListWithWrongData() {
      RestDataCaster::complex(1, XPClass::forName('lang.types.ArrayList'));
    }
    
    /**
     * Test complexify hash map with wrong data
     * 
     */
    #[@test, @expect('lang.ClassCastException')]
    public function complexifyHashmapWithWrongData() {
      RestDataCaster::complex(1, XPClass::forName('util.Hashmap'));
    }
    
    /**
     * Test complexify stdClass with wrong data
     * 
     */
    #[@test, @expect('lang.ClassCastException')]
    public function complexifyStdClassWithWrongData() {
      RestDataCaster::complex(1, new Type('php.stdClass'));
    }
    
    /**
     * Test complexify with wrong target type
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function complexifyWithWrongType() {
      RestDataCaster::complex(1, new Object());
    }
  }
?>
